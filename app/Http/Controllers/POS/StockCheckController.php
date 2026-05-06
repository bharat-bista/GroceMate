<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\FifoStockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockCheckController extends Controller
{
    public function __construct(private FifoStockService $fifoService)
    {
    }

    public function check(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.qty' => ['required', 'numeric', 'gt:0'],
        ]);

        $ids = collect($data['items'])->pluck('product_id')->unique()->values();
        $products = Product::whereIn('id', $ids)->get()->keyBy('id');

        $items = [];
        $allOk = true;

        // Check each item against FIFO availability for the POS channel.
        foreach ($data['items'] as $row) {
            $productId = (int) $row['product_id'];
            $qty = (float) $row['qty'];
            $check = $this->fifoService->canConsume($productId, $qty, 'pos');

            $items[] = [
                'product_id' => $productId,
                'name' => (string) ($products[$productId]->name ?? 'Unknown product'),
                'requested' => $qty,
                'available' => (float) $check['available'],
                'ok' => (bool) $check['ok'],
                'shortfall' => (float) $check['shortfall'],
            ];

            if (!$check['ok']) {
                $allOk = false;
            }
        }

        return response()->json([
            'ok' => $allOk,
            'items' => $items,
        ]);
    }
}
