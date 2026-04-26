<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\EcommerceProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Mail\OrderConfirmationMail;
use App\Mail\OrderCustomerMessageMail;
use App\Services\EcommerceIncomeSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Mail;

class OrderController extends Controller
{
    public function __construct(private EcommerceIncomeSyncService $ecommerceIncomeSyncService)
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

        $deliveryCharges = [
            'inside' => 100,
            'outside' => 200,
            'pickup' => 0,
        ];
        
        $deliveryCharge = $deliveryCharges[$request->delivery] ?? 0;
        $subtotal = round($items->sum(fn (array $item) => $item['price'] * $item['qty']), 2);
        $total = round($subtotal + $deliveryCharge, 2);

        $paymentStatus = 'pending';
        $transactionId = null;
        $paymentSlipPath = null;
        
        if ($request->payment_method === 'esewa') {
            $paymentStatus = 'verified';
            $transactionId = $request->transaction_id ?? 'ESEWA-' . time();
        } elseif ($request->payment_method === 'cod') {
            $paymentStatus = 'cod';
        } elseif ($request->payment_method === 'connectips') {
            $paymentSlipPath = $this->storePaymentSlip($request->payment_slip);
        }

        $order = DB::transaction(function () use ($request, $subtotal, $deliveryCharge, $total, $paymentStatus, $paymentSlipPath, $transactionId, $items) {
            $this->deductEcommerceStock($items);

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
            'message' => 'Order placed successfully!'
        ]);
    }

    private function deductEcommerceStock(Collection $items): void
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

        foreach ($requiredQtyByProduct as $productId => $requiredQty) {
            $product = $products->get((string) $productId);
            $updatedStock = max(0, round((float) $product->ecommerce_stock - (float) $requiredQty, 3));

            $product->ecommerce_stock = $updatedStock;
            $product->status = $updatedStock > 0 ? 'in_stock' : 'out_of_stock';
            $product->save();
        }
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
        $request->validate([
            'delivery_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $nextStatus = (string) $request->delivery_status;
        $isFirstCancelTransition = $order->delivery_status !== 'cancelled' && $nextStatus === 'cancelled';
        $shouldRestoreConnectIpsStock = $isFirstCancelTransition
            && $order->payment_method === 'connectips'
            && $order->payment_status === 'verified';

        DB::transaction(function () use ($order, $nextStatus, $shouldRestoreConnectIpsStock) {
            if ($shouldRestoreConnectIpsStock) {
                $order->loadMissing('items');
                $this->restoreEcommerceStock($order->items);
            }

            $order->update([
                'delivery_status' => $nextStatus,
            ]);

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

            $updatedStock = max(0, round((float) $product->ecommerce_stock + (float) $restoreQty, 3));
            $product->ecommerce_stock = $updatedStock;
            $product->status = $updatedStock > 0 ? 'in_stock' : 'out_of_stock';
            $product->save();
        }
    }

    /**
     * Admin: Update payment status
     */
    public function updatePaymentStatus(Request $request, Order $order)
    {
        if ($request->filled('payment_state')) {
            $request->validate([
                'payment_state' => 'required|in:paid,unpaid',
            ]);

            $finalPaymentStatus = $request->payment_state === 'paid' ? 'verified' : 'pending';
        } else {
            $request->validate([
                'payment_status' => 'required|in:pending,verified,failed,cod',
            ]);

            $finalPaymentStatus = (string) $request->payment_status;
        }

        if ($order->payment_method === 'esewa') {
            $finalPaymentStatus = 'verified';
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
