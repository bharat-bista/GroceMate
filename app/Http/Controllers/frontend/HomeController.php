<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\EcommerceProduct;
use App\Models\Slider;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function home(){
        $cacheUntil = now()->addMinutes(5);

        $heroSlides = Cache::remember('storefront.home.hero_slides', $cacheUntil, function () {
            return Slider::query()
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->where('slider_type', 'hero')
                        ->orWhereNull('slider_type');
                })
                ->orderBy('sort_order')
                ->latest('id')
                ->limit(8)
                ->get();
        });

        $promoSlides = Cache::remember('storefront.home.promo_slides', $cacheUntil, function () {
            return Slider::query()
                ->where('is_active', true)
                ->where('slider_type', 'promo')
                ->orderBy('promo_slot')
                ->orderBy('sort_order')
                ->latest('id')
                ->limit(4)
                ->get();
        });

        $topSaleProducts = Cache::remember('storefront.home.top_sale_products', $cacheUntil, function () {
            return EcommerceProduct::query()
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
                ->limit(12)
                ->get();
        });

        $featuredProducts = Cache::remember('storefront.home.featured_products', $cacheUntil, function () {
            return EcommerceProduct::query()
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
                ->limit(16)
                ->get();
        });

        $brands = Cache::remember('storefront.home.brands', $cacheUntil, function () {
            return Brand::query()
                ->whereHas('ecommerceProducts', fn ($query) => $query->storefrontVisible())
                ->orderBy('order')
                ->orderBy('name')
                ->limit(24)
                ->get();
        });

        $categories = Cache::remember('storefront.home.categories', $cacheUntil, function () {
            return Category::query()
                ->whereHas('ecommerceProducts', fn ($query) => $query->storefrontVisible())
                ->withCount(['ecommerceProducts' => fn ($query) => $query->storefrontVisible()])
                ->orderBy('order')
                ->orderBy('name')
                ->limit(20)
                ->get();
        });

        return view('frontend.home.index', compact('brands', 'categories', 'featuredProducts', 'topSaleProducts', 'heroSlides', 'promoSlides'));
    }
}
