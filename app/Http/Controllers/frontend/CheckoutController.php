<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
            'delivery' => 'required|in:inside,outside',
            'amount' => 'required|numeric|min:1',
        ]);

        $totalAmount     = number_format($request->amount, 2, '.', '');
        $transactionUuid = 'ECOM-' . time() . '-' . Str::random(6);
        $productCode     = config('services.esewa.product_code');
        $secretKey       = config('services.esewa.secret_key');

        $message   = "total_amount={$totalAmount},transaction_uuid={$transactionUuid},product_code={$productCode}";
        $signature = base64_encode(hash_hmac('sha256', $message, $secretKey, true));

        // Store order details in session for callback processing
        session([
            'esewa_checkout_order' => [
                'transaction_uuid' => $transactionUuid,
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'address' => $request->address,
                'delivery' => $request->delivery,
                'amount' => $request->amount,
                'items' => session('gm_checkout_selected_items', []),
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
                ->with('error', '❌ eSewa payment was cancelled or failed.');
        }

        $decoded = json_decode(base64_decode($rawData), true);

        if (!$decoded) {
            return redirect()->route('checkout')
                ->with('error', '❌ Invalid payment data from eSewa.');
        }

        if (($decoded['status'] ?? '') !== 'COMPLETE') {
            return redirect()->route('checkout')
                ->with('error', '❌ eSewa payment not completed. Status: ' . ($decoded['status'] ?? 'unknown'));
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
                ->with('error', '❌ eSewa signature verification failed.');
        }

        $orderInfo = session('esewa_checkout_order');

        if (!$orderInfo) {
            return redirect()->route('checkout')
                ->with('error', '❌ Order information not found.');
        }

        // Clear the selected items and session data
        session()->forget(['esewa_checkout_order', 'gm_checkout_selected_items']);

        // Here you would typically save the order to database
        // For now, we'll just show success message
        
        return redirect()->route('checkout')
            ->with('success', '✅ Payment successful! Order placed. Transaction ID: ' . $decoded['transaction_uuid']);
    }
}
