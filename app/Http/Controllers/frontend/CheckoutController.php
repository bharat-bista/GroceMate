<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Mail\OrderConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mail;

class CheckoutController extends Controller
{
    public function checkout(){
        return view('frontend.checkout.index');
    }

    /**
     * Initiate eSewa Payment for frontend checkout
     */
    public function initiateEsewa(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'delivery' => 'required|in:inside,outside,pickup',
            'amount' => 'required|numeric|min:1',
        ]);

        $totalAmount     = number_format($request->amount, 2, '.', '');
        $transactionUuid = 'ECOM-' . time() . '-' . Str::random(6);
        $productCode     = config('services.esewa.product_code');
        $secretKey       = config('services.esewa.secret_key');

        $message   = "total_amount={$totalAmount},transaction_uuid={$transactionUuid},product_code={$productCode}";
        $signature = base64_encode(hash_hmac('sha256', $message, $secretKey, true));

        // Get items from session
        $items = session('gm_checkout_selected_items', []);

        // Store order details in session for callback processing
        session([
            'esewa_checkout_order' => [
                'transaction_uuid' => $transactionUuid,
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'delivery' => $request->delivery,
                'amount' => $request->amount,
                'items' => $items,
            ]
        ]);

        session()->save();

        $paymentUrl = config('services.esewa.payment_url');
        $successUrl = route('frontend.checkout.esewa.callback');
        $failureUrl = route('frontend.checkout.esewa.callback');

        return view('frontend.checkout.esewa_redirect', compact(
            'paymentUrl', 'totalAmount', 'transactionUuid',
            'productCode', 'signature', 'successUrl', 'failureUrl'
        ));
    }

    /**
     * Handle eSewa callback after payment
     */
    public function esewaCallback(Request $request)
    {
        $rawData = $request->query('data');

        if (!$rawData) {
            return redirect()->route('checkout')
                ->with('error', 'eSewa payment was cancelled or failed.');
        }

        $decoded = json_decode(base64_decode($rawData), true);

        if (!$decoded) {
            return redirect()->route('checkout')
                ->with('error', 'Invalid payment data from eSewa.');
        }

        if (($decoded['status'] ?? '') !== 'COMPLETE') {
            return redirect()->route('checkout')
                ->with('error', 'eSewa payment not completed. Status: ' . ($decoded['status'] ?? 'unknown'));
        }

        // Verify HMAC signature
        $secretKey       = config('services.esewa.secret_key');
        $signedFields    = explode(',', $decoded['signed_field_names']);
        $signatureString = collect($signedFields)
            ->map(fn($field) => "{$field}={$decoded[$field]}")
            ->implode(',');

        $expectedSignature = base64_encode(hash_hmac('sha256', $signatureString, $secretKey, true));

        if ($expectedSignature !== $decoded['signature']) {
            return redirect()->route('checkout')
                ->with('error', 'eSewa signature verification failed.');
        }

        $orderInfo = session('esewa_checkout_order');

        if (!$orderInfo) {
            return redirect()->route('checkout')
                ->with('error', 'Order information not found.');
        }

        // Calculate delivery charge
        $deliveryCharges = [
            'inside' => 100,
            'outside' => 200,
            'pickup' => 0,
        ];
        $deliveryCharge = $deliveryCharges[$orderInfo['delivery']] ?? 0;
        $subtotal = floatval($orderInfo['amount']);
        $total = $subtotal + $deliveryCharge;

        // Create order
        $order = Order::create([
            'order_number' => Order::generateOrderNumber(),
            'customer_name' => $orderInfo['full_name'],
            'customer_phone' => $orderInfo['phone'],
            'customer_email' => $orderInfo['email'] ?? null,
            'delivery_address' => $orderInfo['address'],
            'delivery_type' => $orderInfo['delivery'],
            'subtotal' => $subtotal,
            'delivery_charge' => $deliveryCharge,
            'total_amount' => $total,
            'payment_method' => 'esewa',
            'payment_status' => 'verified',
            'transaction_id' => $decoded['transaction_uuid'],
            'delivery_status' => 'pending',
        ]);

        // Create order items
        foreach ($orderInfo['items'] as $item) {
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
                Mail::to($order->customer_email)->send(new OrderConfirmationMail($order, 'payment_verified'));
            } catch (\Exception $e) {
                // Email sending failed, continue anyway
            }
        }

        // Clear session
        session()->forget(['esewa_checkout_order', 'gm_checkout_selected_items']);

        return redirect()->route('orders')
            ->with('success', 'Payment successful! Order placed. Transaction ID: ' . $decoded['transaction_uuid']);
    }
}