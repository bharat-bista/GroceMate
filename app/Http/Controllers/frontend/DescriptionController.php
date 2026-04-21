<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\EcommerceProduct;

class DescriptionController extends Controller
{
    public function description(?EcommerceProduct $ecommerceProduct = null)
    {
        $selectedProduct = $ecommerceProduct;

        if ($selectedProduct && ((float) $selectedProduct->ecommerce_stock <= 0 || $selectedProduct->status !== 'in_stock')) {
            abort(404, 'This ecommerce product is currently out of stock.');
        }

        if (!$selectedProduct) {
            $selectedProduct = EcommerceProduct::query()
                ->storefrontVisible()
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

        $topSalePool = EcommerceProduct::query()
            ->latestPerProduct()
            ->storefrontVisible()
            ->select(['id', 'product_id', 'discount_percent', 'previous_price', 'mrp', 'display_price', 'thumbnail', 'created_at'])
            ->with([
                'product:id,name,brand_id,category_id',
                'product.category:id,name',
                'product.brandRelation:id,name',
            ])
            ->where('discount_percent', '>', 30)
            ->whereHas('product', function ($query) {
                $query->whereNotNull('category_id')
                    ->whereNotNull('brand_id');
            })
            ->orderByDesc('discount_percent')
            ->latest()
            ->limit(24)
            ->get();

        $topSaleProducts = $topSalePool
            ->where('id', '!=', $selectedProduct->id)
            ->take(12)
            ->values();

        if ($topSaleProducts->isEmpty()) {
            $recentPool = EcommerceProduct::query()
                ->latestPerProduct()
                ->storefrontVisible()
                ->select(['id', 'product_id', 'discount_percent', 'previous_price', 'mrp', 'display_price', 'thumbnail', 'created_at'])
                ->with([
                    'product:id,name,brand_id,category_id',
                    'product.category:id,name',
                    'product.brandRelation:id,name',
                ])
                ->whereHas('product', function ($query) {
                    $query->whereNotNull('category_id')
                        ->whereNotNull('brand_id');
                })
                ->latest()
                ->limit(24)
                ->get();

            $topSaleProducts = $recentPool
                ->where('id', '!=', $selectedProduct->id)
                ->take(12)
                ->values();
        }

        return view('frontend.description.index', compact('selectedProduct', 'topSaleProducts', 'galleryPaths'));
    }
}
