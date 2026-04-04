<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\EcommerceProduct;

class DescriptionController extends Controller
{
    public function description(?EcommerceProduct $ecommerceProduct = null)
    {
        $selectedProduct = $ecommerceProduct;

        if (!$selectedProduct) {
            $selectedProduct = EcommerceProduct::query()
                ->where('status', 'in_stock')
                ->whereHas('product')
                ->latest()
                ->first();
        }

        if (!$selectedProduct) {
            abort(404, 'No ecommerce product found.');
        }

        $selectedProduct->load([
            'product.category',
            'product.brandRelation',
            'images' => function ($query) {
                $query->orderBy('is_primary', 'desc')
                    ->orderBy('sort_order')
                    ->orderBy('id');
            },
        ]);

        $galleryPaths = collect([$selectedProduct->thumbnail])
            ->merge($selectedProduct->images->pluck('image_path'))
            ->filter()
            ->unique()
            ->values();

        $topSaleProducts = EcommerceProduct::query()
            ->with(['product.category', 'product.brandRelation'])
            ->where('status', 'in_stock')
            ->where('discount_percent', '>', 30)
            ->where('id', '!=', $selectedProduct->id)
            ->whereHas('product.category')
            ->whereHas('product.brandRelation')
            ->orderByDesc('discount_percent')
            ->latest()
            ->limit(18)
            ->get()
            ->unique('product_id')
            ->values()
            ->take(12);

        if ($topSaleProducts->isEmpty()) {
            $topSaleProducts = EcommerceProduct::query()
                ->with(['product.category', 'product.brandRelation'])
                ->where('status', 'in_stock')
                ->where('id', '!=', $selectedProduct->id)
                ->whereHas('product.category')
                ->whereHas('product.brandRelation')
                ->latest()
                ->limit(18)
                ->get()
                ->unique('product_id')
                ->values()
                ->take(12);
        }

        return view('frontend.description.index', compact('selectedProduct', 'topSaleProducts', 'galleryPaths'));
    }
}
