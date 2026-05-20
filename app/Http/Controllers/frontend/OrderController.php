<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\DeliveryFeeSetting;
use App\Models\EcommerceProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderRefund;
use App\Exceptions\InsufficientStockException;
use App\Mail\OrderConfirmationMail;
use App\Mail\OrderCustomerMessageMail;
use App\Services\EcommerceIncomeSyncService;
use App\Services\FifoStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Mail;

class OrderController extends Controller
{
    public function __construct(
        private EcommerceIncomeSyncService $ecommerceIncomeSyncService,
        private FifoStockService $fifoStockService
    )
    {
    }

    /**
     * Display customer's orders
     */
    public function index(Request $request)
    {
        $orders = Order::query()
            ->select([
                'id',
                'order_number',
                'delivery_status',
                'payment_status',
                'delivery_type',
                'subtotal',
                'delivery_charge',
                'total_amount',
                'created_at',
                'cancellation_request_status',
            ])
            ->withCount('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'html' => view('frontend.order.partials.list', compact('orders'))->render(),
            ]);
        }

        return view('frontend.order.index', compact('orders'));
    }

    /**
     * Display order details
     */
    public function show(Order $order)
    {
        $order->load('items');
        return view('frontend.order.show', compact('order'));
    }

    /**
     * Store new order from checkout
     */
    public function store(Request $request)
    {
        if (auth()->check() && auth()->user()->canAccessInventoryPanel()) {
            return back()->with('error', 'Admin and staff accounts cannot place orders. Please use a customer account.');
        }

        $request->merge([
            'items' => $this->normalizeItemsPayload($request->input('items')),
        ]);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'required|string|max:500',
            'delivery' => 'required|in:inside,outside,pickup',
            'payment_method' => 'required|in:esewa,connectips,cod',
            'payment_slip' => 'nullable|string|required_if:payment_method,connectips',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required',
            'items.*.name' => 'required|string|max:255',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $items = collect($request->input('items', []))
            ->map(function (array $item) {
                return [
                    'id' => (string) ($item['id'] ?? ''),
                    'name' => (string) ($item['name'] ?? 'Product'),
                    'price' => (float) ($item['price'] ?? 0),
                    'qty' => max(1, (int) ($item['qty'] ?? 1)),
                    'image' => !empty($item['image']) ? (string) $item['image'] : null,
                ];
            })
            ->filter(fn (array $item) => $item['id'] !== '')
            ->values();

        if ($items->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'At least one valid product is required.',
            ]);
        }

        $deliveryCharges = DeliveryFeeSetting::chargeMap();
        
        $deliveryCharge = $deliveryCharges[$request->delivery] ?? 0;
        $subtotal = round($items->sum(fn (array $item) => $item['price'] * $item['qty']), 2);
        $total = round($subtotal + $deliveryCharge, 2);

        $paymentStatus = 'pending';
        $transactionId = null;
        $paymentSlipPath = null;
        
        if ($request->payment_method === 'esewa') {
            $paymentStatus = 'verified';
            $transactionId = $request->transaction_id ?? 'ESEWA-' . time();
        } elseif ($request->payment_method === 'connectips') {
            $paymentSlipPath = $this->storePaymentSlip($request->payment_slip);
        }

        $order = DB::transaction(function () use ($request, $subtotal, $deliveryCharge, $total, $paymentStatus, $paymentSlipPath, $transactionId, $items) {
            $batchesByProduct = $this->deductEcommerceStock($items);

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'customer_name' => $request->full_name,
                'customer_phone' => $request->phone,
                'customer_email' => $request->email ?? null,
                'delivery_address' => $request->address,
                'delivery_type' => $request->delivery,
                'subtotal' => $subtotal,
                'delivery_charge' => $deliveryCharge,
                'total_amount' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'payment_slip' => $paymentSlipPath,
                'transaction_id' => $transactionId,
                'delivery_status' => 'pending',
                'notes' => $request->notes ?? null,
            ]);

            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['qty'],
                    'subtotal' => round($item['price'] * $item['qty'], 2),
                    'image' => $item['image'],
                    'batches_consumed' => $batchesByProduct[$item['id']] ?? null,
                ]);
            }

            $this->ecommerceIncomeSyncService->syncOrder($order);

            return $order;
        });

        // Send email notification if customer email exists
        if ($order->customer_email) {
            try {
                Mail::to($order->customer_email)->send(new OrderConfirmationMail($order));
            } catch (\Exception $e) {
                // Email sending failed, continue anyway
            }
        }

        return response()->json([
            'success' => true,
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'delivery_type' => $order->delivery_type,
            'delivery_charge' => (float) $order->delivery_charge,
            'total_amount' => (float) $order->total_amount,
            'payment_method' => $order->payment_method,
            'message' => 'Order placed successfully!'
        ]);
    }

    /**
     * Returns live ecommerce_stock for given product IDs (used by the cart page).
     * GET /cart/stock?ids[]=1&ids[]=2
     */
    public function getCartStock(Request $request)
    {
        $ids = array_filter(array_map('intval', (array) $request->query('ids', [])));

        if (empty($ids)) {
            return response()->json(['stock' => []]);
        }

        $products = EcommerceProduct::query()
            ->whereIn('id', $ids)
            ->get(['id', 'ecommerce_stock', 'status']);

        $stock = $products->mapWithKeys(function (EcommerceProduct $product) {
            return [
                (string) $product->id => [
                    'stock' => (float) $product->ecommerce_stock,
                    'status' => $product->status,
                ],
            ];
        });

        return response()->json(['stock' => $stock]);
    }

    public function validateStock(Request $request)
    {
        try {
            $request->merge([
                'items' => $this->normalizeItemsPayload($request->input('items')),
            ]);

            $request->validate([
                'items' => 'required|array|min:1',
                'items.*.id' => 'required',
                'items.*.qty' => 'required|integer|min:1',
            ]);

            $requiredQtyByProduct = collect($request->input('items', []))
                ->map(function (array $item) {
                    return [
                        'id' => trim((string) ($item['id'] ?? '')),
                        'qty' => max(1, (int) ($item['qty'] ?? 1)),
                    ];
                })
                ->filter(fn (array $item) => $item['id'] !== '')
                ->groupBy('id')
                ->map(fn (Collection $group) => (int) $group->sum('qty'));

            if ($requiredQtyByProduct->isEmpty()) {
                throw ValidationException::withMessages([
                    'items' => 'No valid ecommerce products were found in the cart.',
                ]);
            }

            $products = EcommerceProduct::query()
                ->with('product:id,name')
                ->whereIn('id', $requiredQtyByProduct->keys()->all())
                ->get()
                ->keyBy(fn (EcommerceProduct $product) => (string) $product->id);

            foreach ($requiredQtyByProduct as $productId => $requiredQty) {
                $product = $products->get((string) $productId);

                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => "Selected ecommerce product ({$productId}) was not found.",
                    ]);
                }

                if ((float) $product->ecommerce_stock < (float) $requiredQty) {
                    throw ValidationException::withMessages([
                        'items' => "Insufficient ecommerce stock for {$product->product?->name}. Available: {$product->ecommerce_stock}, required: {$requiredQty}.",
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Stock is available.',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->errors();

            return response()->json([
                'success' => false,
                'message' => collect($errors)->flatten()->first() ?? 'Unable to validate stock.',
                'errors' => $errors,
            ], 422);
        }
    }

    private function deductEcommerceStock(Collection $items): array
    {
        $requiredQtyByProduct = $items
            ->map(function (array $item) {
                return [
                    'id' => trim((string) ($item['id'] ?? '')),
                    'qty' => max(1, (int) ($item['qty'] ?? 1)),
                ];
            })
            ->filter(fn (array $item) => $item['id'] !== '')
            ->groupBy('id')
            ->map(fn (Collection $group) => (int) $group->sum('qty'));

        if ($requiredQtyByProduct->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'No valid ecommerce products were found in the order.',
            ]);
        }

        $products = EcommerceProduct::query()
            ->whereIn('id', $requiredQtyByProduct->keys()->all())
            ->lockForUpdate()
            ->get()
            ->keyBy(fn (EcommerceProduct $product) => (string) $product->id);

        $batchesByProduct = [];

        foreach ($requiredQtyByProduct as $productId => $requiredQty) {
            $product = $products->get((string) $productId);

            if (!$product) {
                throw ValidationException::withMessages([
                    'items' => "Selected ecommerce product ({$productId}) was not found.",
                ]);
            }

            if ((float) $product->ecommerce_stock < (float) $requiredQty) {
                throw ValidationException::withMessages([
                    'items' => "Insufficient ecommerce stock for {$product->product?->name}. Available: {$product->ecommerce_stock}, required: {$requiredQty}.",
                ]);
            }

            $baseProductId = (int) ($product->product_id ?? 0);
            if ($baseProductId <= 0) {
                throw ValidationException::withMessages([
                    'items' => "Unable to resolve inventory product for {$product->product?->name}.",
                ]);
            }

            try {
                $consumeResult = $this->fifoStockService->consume($baseProductId, (float) $requiredQty, 'ecommerce');
                $batchesByProduct[(string) $productId] = $consumeResult['batches_used'] ?? [];
            } catch (InsufficientStockException $e) {
                throw ValidationException::withMessages([
                    'items' => $e->getMessage(),
                ]);
            }
        }

        foreach ($requiredQtyByProduct as $productId => $requiredQty) {
            $product = $products->get((string) $productId);
            $updatedStock = max(0, (int) round((float) $product->ecommerce_stock - (float) $requiredQty));

            $product->ecommerce_stock = $updatedStock;
            $product->status = $updatedStock > 0 ? 'in_stock' : 'out_of_stock';
            $product->save();
        }

        return $batchesByProduct;
    }

    private function normalizeItemsPayload(mixed $items): array
    {
        if (is_array($items)) {
            return $items;
        }

        if (is_string($items) && $items !== '') {
            $decoded = json_decode($items, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    private function storePaymentSlip(?string $paymentSlipData): ?string
    {
        if (!$paymentSlipData) {
            return null;
        }

        if (Str::startsWith($paymentSlipData, ['http://', 'https://', '/storage/'])) {
            return $paymentSlipData;
        }

        if (!Str::startsWith($paymentSlipData, 'data:')) {
            return $paymentSlipData;
        }

        if (!preg_match('/^data:(?<mime>[\w\/\-\.\+]+);base64,(?<data>.+)$/', $paymentSlipData, $matches)) {
            throw ValidationException::withMessages([
                'payment_slip' => 'Invalid payment slip format.',
            ]);
        }

        $allowedMimeMap = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
        ];

        $mime = strtolower($matches['mime']);
        if (!array_key_exists($mime, $allowedMimeMap)) {
            throw ValidationException::withMessages([
                'payment_slip' => 'Unsupported payment slip type. Please upload JPG, PNG, or PDF.',
            ]);
        }

        $binary = base64_decode(str_replace(' ', '+', $matches['data']), true);
        if ($binary === false) {
            throw ValidationException::withMessages([
                'payment_slip' => 'Invalid payment slip file data.',
            ]);
        }

        if (strlen($binary) > (5 * 1024 * 1024)) {
            throw ValidationException::withMessages([
                'payment_slip' => 'Payment slip must be less than 5MB.',
            ]);
        }

        $extension = $allowedMimeMap[$mime];
        $path = 'order-payment-slips/' . now()->format('Y/m') . '/' . Str::uuid() . '.' . $extension;
        Storage::disk('public')->put($path, $binary);

        return $path;
    }

    /**
     * Admin: List all orders
     */
    public function adminIndex(Request $request)
    {
        $query = Order::with('items')->orderBy('created_at', 'desc');

        if ($request->status) {
            $query->where('delivery_status', $request->status);
        }

        if ($request->payment_status) {
            $this->applyPaymentFilter($query, (string) $request->payment_status);
        }

        if ($request->search) {
            $query->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $request->search . '%')
                  ->orWhere('customer_phone', 'like', '%' . $request->search . '%');
        }

        $orders = $query->paginate(15);

        return view('frontend.order.admin.index', compact('orders'));
    }

    /**
     * Admin: View order details
     */
    public function adminShow(Order $order)
    {
        $order->load('items');
        return view('frontend.order.admin.show', compact('order'));
    }

    /**
     * Admin: Send custom message to customer
     */
    public function sendCustomerMessage(Request $request, Order $order)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        if (!$order->customer_email) {
            return response()->json([
                'success' => false,
                'message' => 'Customer does not have an email address.',
            ], 400);
        }

        try {
            Mail::to($order->customer_email)->send(new OrderCustomerMessageMail($order, $validated['message']));

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully to ' . $order->customer_email,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send order message: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to send email. Please check your email configuration.',
            ], 500);
        }
    }

    /**
     * Admin: Update delivery status
     */
    public function updateDeliveryStatus(Request $request, Order $order)
    {
        if ($order->isLocked()) {
            return back()->with('error', 'Delivered orders are locked and cannot be updated.');
        }

        if ($order->delivery_status === 'cancelled') {
            return back()->with('error', 'Cancelled orders are locked and cannot be updated.');
        }

        $request->validate([
            'delivery_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $nextStatus = (string) $request->delivery_status;

        if ($nextStatus === 'cancelled' && $order->delivery_status === 'shipped') {
            return back()->with('error', 'Shipped orders cannot be cancelled.');
        }

        $isFirstCancelTransition = $order->delivery_status !== 'cancelled' && $nextStatus === 'cancelled';
        $shouldRestoreStock = $isFirstCancelTransition;
        $shouldCreateRefund = $isFirstCancelTransition && $order->isPaid();

        DB::transaction(function () use ($order, $nextStatus, $shouldRestoreStock, $shouldCreateRefund) {
            if ($shouldRestoreStock) {
                $order->loadMissing('items.ecommerceProduct');
                $this->restoreEcommerceStock($order->items);
            }

            $order->update([
                'delivery_status' => $nextStatus,
            ]);

            if ($shouldCreateRefund) {
                $this->createRefundForOrder($order);
            }

            $this->ecommerceIncomeSyncService->syncOrder($order);
        });

        // Send email notification to customer
        if ($order->customer_email) {
            try {
                // Use an explicit mail type so the email template can render a
                // cancellation-specific message instead of a generic confirmation.
                $mailType = $nextStatus === 'cancelled' ? 'order_cancelled' : 'delivery_update';
                Mail::to($order->customer_email)->send(new OrderConfirmationMail($order, $mailType));
            } catch (\Exception $e) {
                // Email sending failed, continue anyway
            }
        }

        return back()->with('success', 'Delivery status updated successfully!');
    }

    private function restoreEcommerceStock(Collection $items): void
    {
        $restoreQtyByProduct = $items
            ->map(function ($item) {
                return [
                    'id' => trim((string) ($item->product_id ?? '')),
                    'qty' => max(1, (int) ($item->quantity ?? 1)),
                ];
            })
            ->filter(fn (array $item) => $item['id'] !== '')
            ->groupBy('id')
            ->map(fn (Collection $group) => (int) $group->sum('qty'));

        if ($restoreQtyByProduct->isEmpty()) {
            return;
        }

        $products = EcommerceProduct::query()
            ->whereIn('id', $restoreQtyByProduct->keys()->all())
            ->lockForUpdate()
            ->get()
            ->keyBy(fn (EcommerceProduct $product) => (string) $product->id);

        foreach ($restoreQtyByProduct as $productId => $restoreQty) {
            $product = $products->get((string) $productId);

            if (!$product) {
                throw ValidationException::withMessages([
                    'items' => "Unable to restore stock. Ecommerce product ({$productId}) was not found.",
                ]);
            }

            $updatedStock = max(0, (int) round((float) $product->ecommerce_stock + (float) $restoreQty));
            $product->ecommerce_stock = $updatedStock;
            $product->status = $updatedStock > 0 ? 'in_stock' : 'out_of_stock';
            $product->save();
        }

        foreach ($items as $item) {
            $productId = trim((string) ($item->product_id ?? ''));
            if ($productId === '') {
                continue;
            }

            $product = $products->get($productId);
            $baseProductId = (int) ($product?->product_id ?? 0);
            if ($baseProductId <= 0) {
                continue;
            }

            $batchesUsed = [];
            if (!empty($item->batches_consumed)) {
                $batchesUsed = is_array($item->batches_consumed)
                    ? $item->batches_consumed
                    : (json_decode($item->batches_consumed, true) ?: []);
            }

            $this->fifoStockService->reverse($baseProductId, (float) ($item->quantity ?? 0), $batchesUsed);
        }
    }

    private function createRefundForOrder(Order $order): void
    {
        $refund = OrderRefund::firstOrNew(['order_id' => $order->id]);

        $refund->customer_name = $order->customer_name;
        $refund->customer_email = $order->customer_email;
        $refund->customer_phone = $order->customer_phone;
        $refund->refund_amount = (float) $order->total_amount;
        $refund->cancelled_at = $refund->cancelled_at ?? now();

        if (!$refund->exists) {
            $refund->refund_status = 'pending';
        }

        $refund->save();
    }

    /**
     * Admin: Update payment status (COD only — eSewa is auto-verified, bank uses verifyPaymentSlip).
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        if ($order->isLocked()) {
            return back()->with('error', 'Delivered orders are locked and cannot be updated.');
        }

        if ($order->delivery_status === 'cancelled') {
            return back()->with('error', 'Cancelled orders are locked and cannot be updated.');
        }

        // eSewa payment is always auto-verified — no manual override.
        if ($order->payment_method === 'esewa') {
            return back()->with('error', 'eSewa payment is managed automatically and cannot be changed manually.');
        }

        // Bank (Connect IPS) payments must be confirmed via the payment slip verification form.
        if ($order->payment_method === 'connectips') {
            return back()->with('error', 'Bank transfer payment must be verified through the payment slip verification form.');
        }

        if ($order->isPaymentLocked()) {
            return back()->with('error', 'Payment is locked and can only be changed through refunds.');
        }

        if ($request->filled('payment_state')) {
            $request->validate([
                'payment_state' => 'required|in:paid,unpaid',
            ]);

            $finalPaymentStatus = $request->payment_state === 'paid' ? 'verified' : 'pending';
        } else {
            $request->validate([
                'payment_status' => 'required|in:pending,verified,failed',
            ]);

            $finalPaymentStatus = (string) $request->payment_status;
        }

        DB::transaction(function () use ($order, $finalPaymentStatus) {
            $order->update([
                'payment_status' => $finalPaymentStatus,
            ]);

            $this->ecommerceIncomeSyncService->syncOrder($order);
        });

        // Send email notification to customer
        if ($order->customer_email) {
            try {
                $type = $finalPaymentStatus === 'verified' ? 'payment_verified' : 'payment_update';
                Mail::to($order->customer_email)->send(new OrderConfirmationMail($order, $type));
            } catch (\Exception $e) {
                // Email sending failed, continue anyway
            }
        }

        return back()->with('success', 'Payment status updated successfully!');
    }

    /**
     * Admin: Update payment slip verification
     */
    public function verifyPaymentSlip(Request $request, Order $order)
    {
        if ($order->isLocked()) {
            return back()->with('error', 'Delivered orders are locked and cannot be updated.');
        }

        if ($order->delivery_status === 'cancelled') {
            return back()->with('error', 'Cancelled orders are locked and cannot be updated.');
        }

        if ($order->isPaymentLocked()) {
            return back()->with('error', 'Payment is already locked.');
        }

        $request->validate([
            'payment_status' => 'required|in:verified,failed',
        ]);

        DB::transaction(function () use ($order, $request) {
            $order->update([
                'payment_status' => $request->payment_status,
            ]);

            $this->ecommerceIncomeSyncService->syncOrder($order);
        });

        // Send email notification to customer
        if ($order->customer_email) {
            try {
                $type = $request->payment_status === 'verified' ? 'payment_verified' : 'payment_failed';
                Mail::to($order->customer_email)->send(new OrderConfirmationMail($order, $type));
            } catch (\Exception $e) {
                // Email sending failed, continue anyway
            }
        }

        return back()->with('success', 'Payment verification updated!');
    }

    /**
     * Admin: Export orders
     */
    public function export(Request $request, $type = 'csv')
    {
        $query = Order::with('items');

        $search = trim((string) $request->get('search', ''));
        $status = trim((string) $request->get('status', ''));
        $paymentStatus = trim((string) $request->get('payment_status', ''));
        $from = $request->get('from');
        $to = $request->get('to');

        if ($status !== '') {
            $query->where('delivery_status', $status);
        }

        if ($paymentStatus !== '') {
            $this->applyPaymentFilter($query, $paymentStatus);
        }

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('order_number', 'like', '%' . $search . '%')
                    ->orWhere('customer_name', 'like', '%' . $search . '%')
                    ->orWhere('customer_phone', 'like', '%' . $search . '%');
            });
        }

        if ($from && $to) {
            try {
                $fromDate = \Carbon\Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
                $toDate = \Carbon\Carbon::createFromFormat('Y-m-d', $to)->endOfDay();
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            } catch (\Exception $e) {
                // Ignore invalid date format and continue exporting with available filters.
            }
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        if ($type === 'csv') {
            $filename = 'orders_' . now()->format('Y-m-d') . '.csv';
            
            $headers = [
                "Content-Type" => "text/csv",
                "Content-Disposition" => "attachment; filename=\"$filename\"",
            ];
            
            $callback = function () use ($orders) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['Order #', 'Customer Name', 'Phone', 'Email', 'Items', 'Subtotal', 'Delivery', 'Total', 'Payment Method', 'Payment Status', 'Delivery Status', 'Date']);
                
                foreach ($orders as $order) {
                    fputcsv($file, [
                        $order->order_number,
                        $order->customer_name,
                        $order->customer_phone,
                        $order->customer_email ?? '',
                        $order->items->count(),
                        number_format($order->subtotal, 2),
                        number_format($order->delivery_charge, 2),
                        number_format($order->total_amount, 2),
                        $order->payment_method,
                        $order->payment_status,
                        $order->delivery_status,
                        $order->created_at->format('Y-m-d H:i'),
                    ]);
                }
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        }

        if ($type === 'pdf') {
            $filename = 'orders_' . now()->format('Y-m-d') . '.pdf';

            $html = view('frontend.order.admin.export-pdf', [
                'orders' => $orders,
                'search' => $search,
                'status' => $status,
                'paymentStatus' => $paymentStatus,
                'from' => $from,
                'to' => $to,
            ])->render();

            $pdf = new \Dompdf\Dompdf();
            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'landscape');
            $pdf->render();

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);
        }

        if ($type === 'excel') {
            $filename = 'orders_' . now()->format('Y-m-d') . '.xlsx';

            $html = view('frontend.order.admin.export-excel', [
                'orders' => $orders,
                'search' => $search,
                'status' => $status,
                'paymentStatus' => $paymentStatus,
                'from' => $from,
                'to' => $to,
            ])->render();

            return response($html, 200, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);
        }

        abort(404);
    }

    /**
     * Customer: Request order cancellation within 30 minutes
     */
    public function requestCancellation(Request $request, Order $order)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        if (!$order->canRequestCancellation()) {
            return response()->json([
                'success' => false,
                'message' => 'This order is no longer eligible for cancellation. The 30-minute window may have passed or the order has already been cancelled/delivered.',
            ]);
        }

        $order->update([
            'cancellation_request_status' => 'pending',
            'cancellation_request_reason' => $validated['reason'],
            'cancellation_requested_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cancellation request submitted.',
        ]);
    }

    /**
     * Admin: Approve cancellation request
     */
    public function approveCancellationRequest(Request $request, Order $order)
    {
        if ($order->cancellation_request_status !== 'pending') {
            return back()->with('error', 'No pending cancellation request for this order.');
        }

        $isFirstCancelTransition = $order->delivery_status !== 'cancelled';
        $shouldRestoreStock = $isFirstCancelTransition;
        $shouldCreateRefund = $isFirstCancelTransition && $order->isPaid();

        DB::transaction(function () use ($order, $shouldRestoreStock, $shouldCreateRefund) {
            $order->update(['cancellation_request_status' => 'approved']);

            if ($shouldRestoreStock) {
                $order->loadMissing('items.ecommerceProduct');
                $this->restoreEcommerceStock($order->items);
            }

            $order->update(['delivery_status' => 'cancelled']);

            if ($shouldCreateRefund) {
                $this->createRefundForOrder($order);
            }

            $this->ecommerceIncomeSyncService->syncOrder($order);
        });

        if ($order->customer_email) {
            try {
                Mail::to($order->customer_email)->send(new OrderConfirmationMail($order, 'order_cancelled'));
            } catch (\Exception $e) {
                // Email sending failed, continue anyway
            }
        }

        return back()->with('success', 'Cancellation request approved and order cancelled.');
    }

    /**
     * Admin: Reject cancellation request
     */
    public function rejectCancellationRequest(Request $request, Order $order)
    {
        if ($order->cancellation_request_status !== 'pending') {
            return back()->with('error', 'No pending cancellation request for this order.');
        }

        $order->update([
            'cancellation_request_status' => 'rejected',
            'cancellation_requested_at' => null,
        ]);

        return back()->with('success', 'Cancellation request rejected.');
    }

    private function applyPaymentFilter($query, string $paymentFilter): void
    {
        if ($paymentFilter === 'paid') {
            $query->where('payment_status', 'verified');
            return;
        }

        if ($paymentFilter === 'unpaid') {
            $query->whereIn('payment_status', ['pending', 'failed', 'cod']);
            return;
        }

        $query->where('payment_status', $paymentFilter);
    }
}
