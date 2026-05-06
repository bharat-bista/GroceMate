<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\StockBatch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FifoStockService
{
    public function consume(int $productId, float $qtyNeeded): array
    {
        return DB::transaction(function () use ($productId, $qtyNeeded) {
            $qtyNeeded = (float) $qtyNeeded;

            if ($qtyNeeded <= 0) {
                return [
                    'success' => true,
                    'consumed' => [],
                    'total_cogs' => 0.0,
                    'shortfall' => 0.0,
                ];
            }

            $batches = StockBatch::active()
                ->where('product_id', $productId)
                ->fifo()
                ->lockForUpdate()
                ->get();

            $remaining = $qtyNeeded;
            $consumed = [];
            $totalCogs = 0.0;
            $totalConsumed = 0.0;

            foreach ($batches as $batch) {
                if ($remaining <= 0) {
                    break;
                }

                $available = (float) $batch->qty_remaining;
                if ($available <= 0) {
                    continue;
                }

                $consumeQty = min($available, $remaining);
                $newRemaining = $available - $consumeQty;

                $batch->qty_remaining = $newRemaining > 0 ? $newRemaining : 0;
                if ($batch->qty_remaining <= 0) {
                    $batch->status = 'depleted';
                }
                $batch->save();

                $consumed[] = [
                    'batch_no' => $batch->batch_no,
                    'qty_consumed' => $consumeQty,
                    'unit_cost' => (float) $batch->unit_cost,
                ];

                $totalCogs += $consumeQty * (float) $batch->unit_cost;
                $totalConsumed += $consumeQty;
                $remaining -= $consumeQty;
            }

            if ($totalConsumed > 0) {
                $stock = Stock::firstOrCreate(
                    ['product_id' => $productId],
                    ['quantity' => 0, 'reorder_level' => 0]
                );
                $stock->decrement('quantity', $totalConsumed);
            }

            $shortfall = $remaining > 0 ? $remaining : 0.0;

            return [
                'success' => $shortfall <= 0,
                'consumed' => $consumed,
                'total_cogs' => $totalCogs,
                'shortfall' => $shortfall,
            ];
        });
    }

    public function getAvailableQty(int $productId): float
    {
        return (float) StockBatch::active()
            ->where('product_id', $productId)
            ->sum('qty_remaining');
    }

    public function getStockValuation(int $productId): float
    {
        $total = StockBatch::active()
            ->where('product_id', $productId)
            ->selectRaw('SUM(qty_remaining * unit_cost) as total')
            ->value('total');

        return (float) ($total ?? 0);
    }

    public function markExpiredBatches(): int
    {
        $today = Carbon::today()->toDateString();

        return StockBatch::query()
            ->where('status', 'active')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', $today)
            ->where('qty_remaining', '>', 0)
            ->update(['status' => 'expired']);
    }
}
