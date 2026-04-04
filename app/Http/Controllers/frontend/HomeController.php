<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\EcommerceProduct;

class HomeController extends Controller
{
    public function home(){
        $topSaleLimit = 12;

        $topSaleProducts = EcommerceProduct::query()
            ->with(['product.category', 'product.brandRelation'])
            ->where('status', 'in_stock')
            ->where('discount_percent', '>', 30)
            ->whereHas('product.category')
            ->whereHas('product.brandRelation')
            ->orderByDesc('discount_percent')
            ->latest()
            ->limit($topSaleLimit)
            ->get();

        $featuredLimit = 16; // 4 rows x 4 cards target
        $maxPerCategory = 2;

        $ecommerceCandidates = EcommerceProduct::query()
            ->with(['product.category', 'product.brandRelation'])
            ->where('status', 'in_stock')
            ->whereHas('product.category')
            ->whereHas('product.brandRelation')
            ->latest()
            ->get();

        $featuredProducts = collect();
        $perCategoryCounts = [];

        foreach ($ecommerceCandidates as $candidate) {
            $categoryId = $candidate->product?->category_id;

            if (!$categoryId) {
                continue;
            }

            if (($perCategoryCounts[$categoryId] ?? 0) >= $maxPerCategory) {
                continue;
            }

            $featuredProducts->push($candidate);
            $perCategoryCounts[$categoryId] = ($perCategoryCounts[$categoryId] ?? 0) + 1;

            if ($featuredProducts->count() >= $featuredLimit) {
                break;
            }
        }

        // If not enough products after balancing, fill remaining slots with newest items.
        if ($featuredProducts->count() < $featuredLimit) {
            $selectedIds = $featuredProducts->pluck('id')->all();

            foreach ($ecommerceCandidates as $candidate) {
                if (in_array($candidate->id, $selectedIds, true)) {
                    continue;
                }

                $featuredProducts->push($candidate);

                if ($featuredProducts->count() >= $featuredLimit) {
                    break;
                }
            }
        }

        $brands = Brand::whereHas('ecommerceProducts')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $categories = Category::whereHas('ecommerceProducts')
            ->withCount('ecommerceProducts')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return view('frontend.home.index', compact('brands', 'categories', 'featuredProducts', 'topSaleProducts'));
    }
}
