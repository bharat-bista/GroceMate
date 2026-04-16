<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\EcommerceProduct;
use App\Models\Slider;

class HomeController extends Controller
{
    public function home(){
        $topSaleLimit = 12;

        $heroSlides = Slider::query()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('slider_type', 'hero')
                    ->orWhereNull('slider_type');
            })
            ->orderBy('sort_order')
            ->latest('id')
            ->limit(8)
            ->get();

        $promoSlides = Slider::query()
            ->where('is_active', true)
            ->where('slider_type', 'promo')
            ->orderBy('promo_slot')
            ->orderBy('sort_order')
            ->latest('id')
            ->limit(4)
            ->get();

        $topSaleProducts = EcommerceProduct::query()
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
            ->limit($topSaleLimit)
            ->get();

        $featuredLimit = 16; // 4 rows x 4 cards target

        $featuredProducts = EcommerceProduct::query()
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
            ->limit($featuredLimit)
            ->get();

        $brands = Brand::whereHas('ecommerceProducts', fn ($query) => $query->storefrontVisible())
            ->orderBy('order')
            ->orderBy('name')
            ->limit(24)
            ->get();

        $categories = Category::whereHas('ecommerceProducts', fn ($query) => $query->storefrontVisible())
            ->withCount(['ecommerceProducts' => fn ($query) => $query->storefrontVisible()])
            ->orderBy('order')
            ->orderBy('name')
            ->limit(20)
            ->get();

        return view('frontend.home.index', compact('brands', 'categories', 'featuredProducts', 'topSaleProducts', 'heroSlides', 'promoSlides'));
    }
}
