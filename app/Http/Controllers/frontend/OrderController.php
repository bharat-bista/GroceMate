<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Mail\OrderConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Mail;

class OrderController extends Controller
{
    /**
     * Display customer's orders
     */
    public function index()
    {
        $orders = Order::with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

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
     * Admin: Update delivery status
     */
    public function updateDeliveryStatus(Request $request, Order $order)
    {
        $request->validate([
            'delivery_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update([
            'delivery_status' => $request->delivery_status,
        ]);

        // Send email notification to customer
        if ($order->customer_email) {
            try {
                Mail::to($order->customer_email)->send(new OrderConfirmationMail($order, 'delivery_update'));
            } catch (\Exception $e) {
                // Email sending failed, continue anyway
            }
        }

        return back()->with('success', 'Delivery status updated successfully!');
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

        $order->update([
            'payment_status' => $finalPaymentStatus,
        ]);

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

        $order->update([
            'payment_status' => $request->payment_status,
        ]);

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

        if ($request->status) {
            $query->where('delivery_status', $request->status);
        }

        if ($request->payment_status) {
            $this->applyPaymentFilter($query, (string) $request->payment_status);
        }

        if ($request->from && $request->to) {
            $query->whereBetween('created_at', [
                \Carbon\Carbon::createFromFormat('Y-m-d', $request->from)->startOfDay(),
                \Carbon\Carbon::createFromFormat('Y-m-d', $request->to)->endOfDay(),
            ]);
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

        // For PDF or other types, return a view
        return view('frontend.order.admin.export', compact('orders'));
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
