<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Product;
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
}
