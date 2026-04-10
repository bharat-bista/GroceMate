<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Mail\OrderConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'delivery' => 'required|in:inside,outside,pickup',
            'payment_method' => 'required|in:esewa,connectips,cod',
            'amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
        ]);

        $items = json_decode($request->items, true);
        
        $deliveryCharges = [
            'inside' => 100,
            'outside' => 200,
            'pickup' => 0,
        ];
        
        $deliveryCharge = $deliveryCharges[$request->delivery] ?? 0;
        $subtotal = floatval($request->amount);
        $total = $subtotal + $deliveryCharge;

        $paymentStatus = 'pending';
        $transactionId = null;
        
        if ($request->payment_method === 'esewa') {
            $paymentStatus = 'verified';
            $transactionId = $request->transaction_id ?? 'ESEWA-' . time();
        } elseif ($request->payment_method === 'cod') {
            $paymentStatus = 'cod';
        }

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
            'payment_slip' => $request->payment_slip ?? null,
            'transaction_id' => $transactionId,
            'delivery_status' => 'pending',
            'notes' => $request->notes ?? null,
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['id'] ?? null,
                'product_name' => $item['name'] ?? 'Product',
                'price' => $item['price'] ?? 0,
                'quantity' => $item['qty'] ?? 1,
                'subtotal' => ($item['price'] ?? 0) * ($item['qty'] ?? 1),
                'image' => $item['image'] ?? null,
            ]);
        }

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
            $query->where('payment_status', $request->payment_status);
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
        $request->validate([
            'payment_status' => 'required|in:pending,verified,failed,cod',
        ]);

        $order->update([
            'payment_status' => $request->payment_status,
        ]);

        // Send email notification to customer
        if ($order->customer_email) {
            try {
                $type = $request->payment_status === 'verified' ? 'payment_verified' : 'payment_update';
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
            $query->where('payment_status', $request->payment_status);
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
}