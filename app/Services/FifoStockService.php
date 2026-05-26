<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\EcommerceProduct;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockBatch;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FifoStockService
{
    public function __construct(
        private StockBatch $stockBatch,
        private Stock $stock,
        private EcommerceProduct $ecommerceProduct,
        private Product $product
    ) {
    }

    /**
     * Get total, ecommerce-reserved, and POS-available quantities.
     *
     * @return array{total: float, ecommerce: float, pos: float}
     */
    public function getAvailable(int $productId): array
    {
        // Total FIFO-eligible stock from active batches.
        $total = (float) $this->stockBatch->newQuery()
            ->where('product_id', $productId)
            ->where('status', 'active')
            ->sum('qty_remaining');

        // Reserved quantity for ecommerce (0 if not configured).
        $ecommerce = (float) ($this->ecommerceProduct->newQuery()
            ->where('product_id', $productId)
            ->value('ecommerce_stock') ?? 0);

        // POS can only use what is not reserved for ecommerce.
        $pos = max(0.0, $total - $ecommerce);

        return [
            'total' => $total,
            'ecommerce' => $ecommerce,
            'pos' => $pos,
        ];
    }

    /**
     * Check if a quantity can be consumed from the given channel.
     *
     * @return array{ok: bool, available: float, shortfall: float}
     */
    public function canConsume(int $productId, float $qty, string $channel = 'pos'): array
    {
        $qty = (float) $qty;
        $channel = strtolower($channel);

        if (!in_array($channel, ['pos', 'ecommerce'], true)) {
            throw new InvalidArgumentException('Channel must be "pos" or "ecommerce".');
        }

        $available = $this->getAvailable($productId);
        $usable = $channel === 'ecommerce'
            ? (float) $available['total']
            : (float) $available['pos'];

        $shortfall = max(0.0, $qty - $usable);

        return [
            'ok' => $shortfall <= 0,
            'available' => $usable,
            'shortfall' => $shortfall,
        ];
    }

    /**
     * Consume stock using FIFO rules for the requested channel.
     *
     * @return array{success: bool, consumed: float, batches_used: array<int, array{batch_id: int, qty_taken: float, unit_cost: float}>}
     *
     * @throws InsufficientStockException
     */
    public function consume(int $productId, float $qty, string $channel = 'pos'): array
    {
        return DB::transaction(function () use ($productId, $qty, $channel) {
            // Validate availability before touching any rows.
            $check = $this->canConsume($productId, $qty, $channel);
            if (!$check['ok']) {
                $productName = (string) ($this->product->newQuery()
                    ->where('id', $productId)
                    ->value('name') ?? 'Unknown product');

                throw new InsufficientStockException($productName, (float) $qty, (float) $check['available']);
            }

            $qty = (float) $qty;
            if ($qty <= 0) {
                return [
                    'success' => true,
                    'consumed' => 0.0,
                    'batches_used' => [],
                ];
            }

            // Lock active batches and consume the oldest ones first.
            $batches = $this->stockBatch->newQuery()
                ->active()
                ->where('product_id', $productId)
                ->orderBy('purchased_on')
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $remaining = $qty;
            $batchesUsed = [];
            $totalConsumed = 0.0;

            foreach ($batches as $batch) {
                if ($remaining <= 0) {
                    break;
                }

                $available = (float) $batch->qty_remaining;
                if ($available <= 0) {
                    continue;
                }

                $takeQty = min($available, $remaining);
                $batch->qty_remaining = $available - $takeQty;

                if ($batch->qty_remaining <= 0) {
                    $batch->qty_remaining = 0;
                    $batch->status = 'depleted';
                }

                $batch->save();

                $batchesUsed[] = [
                    'batch_id' => $batch->id,
                    'qty_taken' => $takeQty,
                    'unit_cost' => (float) $batch->unit_cost,
                ];

                $totalConsumed += $takeQty;
                $remaining -= $takeQty;
            }

            if ($totalConsumed > 0) {
                // Keep the running stock cache in sync.
                $stock = $this->stock->newQuery()->firstOrCreate(
                    ['product_id' => $productId],
                    ['quantity' => 0, 'reorder_level' => 0]
                );
                $stock->decrement('quantity', $totalConsumed);
            }

            return [
                'success' => true,
                'consumed' => $totalConsumed,
                'batches_used' => $batchesUsed,
            ];
        });
    }

    /**
     * Return the POS-available quantity for a single batch, after FIFO-distributing
     * the product's ecommerce reservation across older batches first.
     */
    public function batchPosAvailable(int $batchId): float
    {
        $batch = $this->stockBatch->newQuery()
            ->where('id', $batchId)
            ->where('status', 'active')
            ->first(['id', 'product_id', 'qty_remaining']);

        if (!$batch) {
            return 0.0;
        }

        $productId = $batch->product_id;

        $ecommerceReserved = (float) ($this->ecommerceProduct->newQuery()
            ->where('product_id', $productId)
            ->value('ecommerce_stock') ?? 0);

        if ($ecommerceReserved <= 0.0) {
            return (float) $batch->qty_remaining;
        }

        // Walk active batches in FIFO order, subtracting ecommerce reservation from each.
        $batches = $this->stockBatch->newQuery()
            ->where('product_id', $productId)
            ->where('status', 'active')
            ->orderBy('purchased_on')
            ->orderBy('id')
            ->get(['id', 'qty_remaining']);

        $toSubtract = $ecommerceReserved;
        foreach ($batches as $b) {
            $rawQty  = (float) $b->qty_remaining;
            $subtract = min($rawQty, $toSubtract);
            $posQty  = $rawQty - $subtract;
            $toSubtract -= $subtract;

            if ($b->id === $batchId) {
                return max(0.0, $posQty);
            }
        }

        return 0.0;
    }

    /**
     * Consume stock from one specific batch (user-selected, bypasses FIFO order).
     * Returns the same shape as consume() so the reverse() path works unchanged.
     *
     * @return array{success: bool, consumed: float, batches_used: array<int, array{batch_id: int, qty_taken: float, unit_cost: float}>}
     *
     * @throws InsufficientStockException
     */
    public function consumeFromBatch(int $batchId, float $qty): array
    {
        return DB::transaction(function () use ($batchId, $qty) {
            $batch = $this->stockBatch->newQuery()
                ->where('id', $batchId)
                ->where('status', 'active')
                ->where('qty_remaining', '>', 0)
                ->lockForUpdate()
                ->firstOrFail();

            if ((float) $batch->qty_remaining < $qty) {
                $productName = (string) ($this->product->newQuery()
                    ->where('id', $batch->product_id)
                    ->value('name') ?? 'Unknown product');

                throw new InsufficientStockException($productName, $qty, (float) $batch->qty_remaining);
            }

            $batch->qty_remaining = (float) $batch->qty_remaining - $qty;
            if ($batch->qty_remaining <= 0) {
                $batch->qty_remaining = 0;
                $batch->status        = 'depleted';
            }
            $batch->save();

            $stock = $this->stock->newQuery()->firstOrCreate(
                ['product_id' => $batch->product_id],
                ['quantity' => 0, 'reorder_level' => 0]
            );
            $stock->decrement('quantity', $qty);

            return [
                'success'      => true,
                'consumed'     => $qty,
                'batches_used' => [[
                    'batch_id'  => $batch->id,
                    'qty_taken' => $qty,
                    'unit_cost' => (float) $batch->unit_cost,
                ]],
            ];
        });
    }

    /**
     * Reverse a prior consumption by restoring batches or creating a new batch.
     */
    public function reverse(int $productId, float $qty, array $batchesUsed = []): void
    {
        DB::transaction(function () use ($productId, $qty, $batchesUsed) {
            $qty = (float) $qty;
            if ($qty <= 0) {
                return;
            }

            if (!empty($batchesUsed)) {
                // Restore quantities to the exact batches used (LIFO to mirror FIFO consumption).
                foreach (array_reverse($batchesUsed) as $item) {
                    if (!isset($item['batch_id'], $item['qty_taken'])) {
                        throw new InvalidArgumentException('Each batchesUsed entry must include batch_id and qty_taken.');
                    }

                    $restoreQty = (float) $item['qty_taken'];
                    if ($restoreQty <= 0) {
                        throw new InvalidArgumentException('qty_taken must be greater than zero.');
                    }

                    $batch = $this->stockBatch->newQuery()
                        ->where('id', (int) $item['batch_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    $batch->qty_remaining = (float) $batch->qty_remaining + $restoreQty;
                    if ($batch->status === 'depleted' && $batch->qty_remaining > 0) {
                        $batch->status = 'active';
                    }

                    $batch->save();
                }
            } else {
                // No batch trace available: create one new active batch for the returned qty.
                $today = Carbon::today()->toDateString();

                $totals = $this->stockBatch->newQuery()
                    ->where('product_id', $productId)
                    ->where('status', 'active')
                    ->selectRaw('SUM(qty_remaining) as total_qty, SUM(qty_remaining * unit_cost) as total_cost')
                    ->first();

                $totalQty = (float) ($totals->total_qty ?? 0);
                $totalCost = (float) ($totals->total_cost ?? 0);
                $unitCost = $totalQty > 0 ? $totalCost / $totalQty : 0.0;

                $this->stockBatch->newQuery()->create([
                    'product_id' => $productId,
                    'batch_no' => StockBatch::generateBatchNo($today),
                    'qty_received' => $qty,
                    'qty_remaining' => $qty,
                    'unit_cost' => $unitCost,
                    'purchased_on' => $today,
                    'status' => 'active',
                ]);
            }

            // Update the stock cache after restoring inventory.
            $stock = $this->stock->newQuery()->firstOrCreate(
                ['product_id' => $productId],
                ['quantity' => 0, 'reorder_level' => 0]
            );
            $stock->increment('quantity', $qty);
        });
    }
}
