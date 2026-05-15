<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\DeliveryFeeSetting;
use App\Models\EcommerceProduct;
use App\Models\Order;
use App\Models\OrderItem;
use App\Mail\OrderConfirmationMail;
use App\Exceptions\InsufficientStockException;
use App\Services\EcommerceIncomeSyncService;
use App\Services\FifoStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Mail;

class CheckoutController extends Controller
{
    public function __construct(
        private EcommerceIncomeSyncService $ecommerceIncomeSyncService,
        private FifoStockService $fifoStockService
    )
    {
    }

    public function checkout(){
        return view('frontend.checkout.index', [
            'deliveryFees' => DeliveryFeeSetting::chargeMap(),
        ]);
    }

    /**
     * Initiate eSewa Payment for frontend checkout
     */
    public function initiateEsewa(Request $request)
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
            ->values()
            ->all();

        $deliveryCharges = DeliveryFeeSetting::chargeMap();
        $deliveryCharge = $deliveryCharges[$request->delivery] ?? 0;
        $subtotal = round(collect($items)->sum(fn (array $item) => $item['price'] * $item['qty']), 2);
        $computedTotal = round($subtotal + $deliveryCharge, 2);
        $totalAmount = number_format($computedTotal, 2, '.', '');
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
                'email' => $request->email,
                'address' => $request->address,
                'delivery' => $request->delivery,
                'subtotal' => $subtotal,
                'delivery_charge' => $deliveryCharge,
                'total_amount' => $computedTotal,
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

        $expectedTotal = round((float) ($orderInfo['total_amount'] ?? 0), 2);
        $paidTotal = round((float) ($decoded['total_amount'] ?? 0), 2);

        if (abs($expectedTotal - $paidTotal) > 0.01) {
            return redirect()->route('checkout')
                ->with('error', 'Paid amount does not match the checkout amount.');
        }

        $existingOrder = Order::query()
            ->where('payment_method', 'esewa')
            ->where('transaction_id', $decoded['transaction_uuid'])
            ->first();

        if ($existingOrder) {
            session()->forget(['esewa_checkout_order', 'gm_checkout_selected_items']);
            return redirect()->route('orders')
                ->with('order_confirmation', [
                    'order_id' => $existingOrder->id,
                    'order_number' => $existingOrder->order_number,
                    'delivery_type' => $existingOrder->delivery_type,
                    'delivery_charge' => (float) $existingOrder->delivery_charge,
                    'total_amount' => (float) $existingOrder->total_amount,
                    'payment_method' => 'esewa',
                ]);
        }

        $subtotal = round((float) ($orderInfo['subtotal'] ?? 0), 2);
        $deliveryCharge = round((float) ($orderInfo['delivery_charge'] ?? 0), 2);
        $total = round((float) ($orderInfo['total_amount'] ?? ($subtotal + $deliveryCharge)), 2);

        $order = DB::transaction(function () use ($orderInfo, $subtotal, $deliveryCharge, $total, $decoded) {
            $batchesByProduct = $this->deductEcommerceStock(collect($orderInfo['items'] ?? []));

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

            foreach (($orderInfo['items'] ?? []) as $item) {
                $price = (float) ($item['price'] ?? 0);
                $qty = max(1, (int) ($item['qty'] ?? 1));

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => (string) ($item['id'] ?? ''),
                    'product_name' => (string) ($item['name'] ?? 'Product'),
                    'price' => $price,
                    'quantity' => $qty,
                    'subtotal' => round($price * $qty, 2),
                    'image' => !empty($item['image']) ? (string) $item['image'] : null,
                    'batches_consumed' => $batchesByProduct[(string) ($item['id'] ?? '')] ?? null,
                ]);
            }

            $this->ecommerceIncomeSyncService->syncOrder($order);

            return $order;
        });

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
            ->with('order_confirmation', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'delivery_type' => $order->delivery_type,
                'delivery_charge' => (float) $order->delivery_charge,
                'total_amount' => (float) $order->total_amount,
                'payment_method' => 'esewa',
            ]);
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
            $updatedStock = max(0, round((float) $product->ecommerce_stock - (float) $requiredQty, 3));

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
}
