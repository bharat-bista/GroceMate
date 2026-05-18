<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockBatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductSearchController extends Controller
{
    public function searchProductsForPOS(Request $request): JsonResponse
    {
        $q = trim($request->get('q', ''));
        $businessId = $request->get('business_id');
        $showAll = $request->boolean('show_all');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        // POS search should expose only selling price (never purchase cost).
        $products = Product::query()
            ->with(['stock', 'ecommerceProduct'])
            ->when($businessId, fn($query) => $query->where('business_id', $businessId))
            ->where('name', 'LIKE', '%' . $q . '%')
            ->when(!$showAll, function ($query) {
                $query->whereHas('stock', function ($stockQuery) {
                    $stockQuery->where('quantity', '>', 0);
                });
            })
            ->orderBy('name')
            ->limit(10)
            ->get();

        $results = $products->map(function (Product $product) {
            $availableStock = (float) ($product->stock?->quantity ?? 0);

            return [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'unit' => $product->unit,
                'selling_price' => (float) ($product->selling_price ?? 0),
                'available_stock' => $availableStock,
                'pos_available' => $product->posAvailableStock(),
                'business_id' => $product->business_id,
            ];
        });

        return response()->json($results);
    }

    public function searchBatchesForPOS(Request $request): JsonResponse
    {
        $q               = trim($request->get('q', ''));
        $businessId      = $request->get('business_id');
        $excludeEcommerce = $request->boolean('exclude_ecommerce');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $batches = StockBatch::active()
            ->with(['product.category', 'product.brandRelation'])
            ->whereHas('product', function ($query) use ($q, $businessId) {
                $query->where('name', 'LIKE', '%' . $q . '%');
                if ($businessId) {
                    $query->where('business_id', $businessId);
                }
            })
            ->when($excludeEcommerce, function ($query) {
                $query->whereHas('product', function ($q2) {
                    $q2->whereDoesntHave('ecommerceProduct', function ($q3) {
                        $q3->where('ecommerce_stock', '>', 0);
                    });
                });
            })
            ->orderBy('purchased_on')
            ->orderBy('id')
            ->limit(20)
            ->get();

        $results = $batches->map(function (StockBatch $batch) {
            $product = $batch->product;

            return [
                'batch_id'      => $batch->id,
                'batch_no'      => $batch->batch_no,
                'product_id'    => $batch->product_id,
                'product_name'  => $product?->name ?? '',
                'unit'          => $product?->unit ?? 'pcs',
                'selling_price' => (float) ($product?->selling_price ?? 0),
                'unit_cost'     => (float) $batch->unit_cost,
                'qty_remaining' => (float) $batch->qty_remaining,
                'expiry_date'   => $batch->expiry_date?->format('Y-m-d'),
                'business_id'   => $product?->business_id,
                'category'      => $product?->category?->name ?? 'N/A',
                'brand'         => $product?->brandRelation?->name ?? 'N/A',
            ];
        });

        return response()->json($results);
    }
}
