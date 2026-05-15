<?php

namespace App\Services;

use App\Models\Order;
use App\Models\POS\Income;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EcommerceIncomeSyncService
{
    public function syncBusinessOrders(int $businessId): void
    {
        $orderIds = DB::table('order_items')
            ->join('ecommerce_products', 'order_items.product_id', '=', 'ecommerce_products.id')
            ->join('products', 'ecommerce_products.product_id', '=', 'products.id')
            ->where('products.business_id', $businessId)
            ->distinct()
            ->pluck('order_items.order_id');

        if ($orderIds->isEmpty()) {
            return;
        }

        Order::query()
            ->whereIn('id', $orderIds->all())
            ->orderBy('id')
            ->get()
            ->each(fn (Order $order) => $this->syncOrder($order));
    }

    public function syncOrder(Order $order): void
    {
        $order->refresh();

        $referencePrefix = $this->buildReferencePrefix($order->id);
        $existingIncomeIds = Income::query()
            ->where('reference_no', 'like', $referencePrefix . '%')
            ->pluck('id');

        if (!$this->isOrderSettled($order)) {
            if ($existingIncomeIds->isNotEmpty()) {
                Income::query()->whereIn('id', $existingIncomeIds->all())->get()->each->delete();
            }

            return;
        }

        $amountsByBusiness = $this->businessBreakdown($order->id);
        $syncedIncomeIds = [];

        foreach ($amountsByBusiness as $businessId => $amount) {
            if ($amount <= 0) {
                continue;
            }

            $referenceNo = $this->buildReferenceNo($order->id, (int) $businessId);
            $payload = [
                'customer_id' => null,
                'business_id' => (int) $businessId,
                'created_by' => auth()->id(),
                'transaction_date' => optional($order->created_at)->toDateString() ?? now()->toDateString(),
                'amount_received' => round((float) $amount, 2),
                'payment_method' => $this->mapPaymentMethod($order),
                'income_type' => 'Sale',
                'description' => 'Ecommerce order income (' . $order->order_number . ')',
                'notes' => 'Auto-synced from ecommerce order #' . $order->id,
            ];

            $income = Income::query()->firstOrNew(['reference_no' => $referenceNo]);
            $income->fill($payload);
            $income->save();

            $syncedIncomeIds[] = $income->id;
        }

        if ($existingIncomeIds->isNotEmpty()) {
            $removeIds = $existingIncomeIds->diff($syncedIncomeIds);
            if ($removeIds->isNotEmpty()) {
                Income::query()->whereIn('id', $removeIds->all())->get()->each->delete();
            }
        }
    }

    private function businessBreakdown(int $orderId): Collection
    {
        return DB::table('order_items')
            ->join('ecommerce_products', 'order_items.product_id', '=', 'ecommerce_products.id')
            ->join('products', 'ecommerce_products.product_id', '=', 'products.id')
            ->where('order_items.order_id', $orderId)
            ->whereNotNull('products.business_id')
            ->groupBy('products.business_id')
            ->selectRaw('products.business_id, SUM(order_items.subtotal) as amount')
            ->pluck('amount', 'products.business_id')
            ->map(fn ($value) => (float) $value);
    }

    private function isOrderSettled(Order $order): bool
    {
        $isPaid = $order->payment_status === 'verified';

        return $isPaid && $order->delivery_status !== 'cancelled';
    }

    private function mapPaymentMethod(Order $order): string
    {
        return match ($order->payment_method) {
            'esewa' => 'Esewa',
            'connectips' => 'bank',
            default => 'cash',
        };
    }

    private function buildReferencePrefix(int $orderId): string
    {
        return 'ECOM-ORDER-' . $orderId . '-BIZ-';
    }

    private function buildReferenceNo(int $orderId, int $businessId): string
    {
        return $this->buildReferencePrefix($orderId) . $businessId;
    }
}
