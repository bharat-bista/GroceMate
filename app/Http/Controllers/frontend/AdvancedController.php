<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\EcommerceProduct;
use Illuminate\Http\Request;

class AdvancedController extends Controller
{
    public function advanced(Request $request)
    {
        $baseQuery = EcommerceProduct::query()
            ->latestPerProduct()
            ->where('status', 'in_stock')
            ->whereHas('product.category')
            ->whereHas('product.brandRelation');

        $priceBounds = (clone $baseQuery)
            ->selectRaw('MIN(COALESCE(NULLIF(display_price, 0), mrp)) as min_price, MAX(COALESCE(NULLIF(display_price, 0), mrp)) as max_price')
            ->first();

        $availableMinPrice = (float) ($priceBounds->min_price ?? 0);
        $availableMaxPrice = (float) ($priceBounds->max_price ?? 0);

        if ($availableMaxPrice < $availableMinPrice) {
            $availableMaxPrice = $availableMinPrice;
        }

        $q = trim((string) $request->input('q', ''));
        $brandId = $request->filled('brand_id') ? (int) $request->input('brand_id') : null;
        $categoryIds = collect((array) $request->input('categories', []))
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values()
            ->all();

        $selectedMinPrice = $request->filled('min_price')
            ? (float) $request->input('min_price')
            : $availableMinPrice;
        $selectedMaxPrice = $request->filled('max_price')
            ? (float) $request->input('max_price')
            : $availableMaxPrice;

        $selectedMinPrice = max($selectedMinPrice, $availableMinPrice);
        $selectedMaxPrice = min($selectedMaxPrice, $availableMaxPrice);

        if ($selectedMinPrice > $selectedMaxPrice) {
            $selectedMinPrice = $availableMinPrice;
            $selectedMaxPrice = $availableMaxPrice;
        }

        $ecommerceProducts = (clone $baseQuery)
            ->with(['product.category', 'product.brandRelation'])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($searchQuery) use ($q) {
                    $searchQuery->whereHas('product', function ($productQuery) use ($q) {
                        $productQuery->where('name', 'like', "%{$q}%");
                    })->orWhereHas('product.brandRelation', function ($brandQuery) use ($q) {
                        $brandQuery->where('name', 'like', "%{$q}%");
                    })->orWhereHas('product.category', function ($categoryQuery) use ($q) {
                        $categoryQuery->where('name', 'like', "%{$q}%");
                    });
                });
            })
            ->when($brandId, function ($query) use ($brandId) {
                $query->whereHas('product', function ($productQuery) use ($brandId) {
                    $productQuery->where('brand_id', $brandId);
                });
            })
            ->when(!empty($categoryIds), function ($query) use ($categoryIds) {
                $query->whereHas('product', function ($productQuery) use ($categoryIds) {
                    $productQuery->whereIn('category_id', $categoryIds);
                });
            })
            ->whereRaw('COALESCE(NULLIF(display_price, 0), mrp) BETWEEN ? AND ?', [$selectedMinPrice, $selectedMaxPrice])
            ->orderByDesc('discount_percent')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $brands = Brand::query()
            ->whereHas('ecommerceProducts', fn ($query) => $query->where('status', 'in_stock'))
            ->orderBy('name')
            ->get(['id', 'name']);

        $categories = Category::query()
            ->whereHas('ecommerceProducts', fn ($query) => $query->where('status', 'in_stock'))
            ->withCount(['ecommerceProducts' => fn ($query) => $query->where('status', 'in_stock')])
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('frontend.advanced.index', compact(
            'ecommerceProducts',
            'brands',
            'categories',
            'q',
            'brandId',
            'categoryIds',
            'availableMinPrice',
            'availableMaxPrice',
            'selectedMinPrice',
            'selectedMaxPrice'
        ));
    }

    public function suggestions(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        if (mb_strlen($q) < 2) {
            return response()->json(['items' => []]);
        }

        $products = EcommerceProduct::query()
            ->latestPerProduct()
            ->with(['product.category:id,name', 'product.brandRelation:id,name'])
            ->where('status', 'in_stock')
            ->where(function ($query) use ($q) {
                $query->whereHas('product', function ($productQuery) use ($q) {
                    $productQuery->where('name', 'like', "%{$q}%");
                })->orWhereHas('product.brandRelation', function ($brandQuery) use ($q) {
                    $brandQuery->where('name', 'like', "%{$q}%");
                })->orWhereHas('product.category', function ($categoryQuery) use ($q) {
                    $categoryQuery->where('name', 'like', "%{$q}%");
                });
            })
            ->latest()
            ->limit(8)
            ->get();

        $productItems = $products->map(function ($item) {
            $product = $item->product;
            if (!$product) {
                return null;
            }

            $metaParts = array_filter([
                $product->brandRelation?->name,
                $product->category?->name,
            ]);

            return [
                'type' => 'product',
                'name' => $product->name,
                'meta' => implode(' - ', $metaParts),
                'url' => route('description', $item->id),
                'image' => $item->thumbnail
                    ? asset('storage/' . $item->thumbnail)
                    : asset('assets/img/product/product1.jpg'),
            ];
        })->filter()->values();

        $brands = Brand::query()
            ->where('name', 'like', "%{$q}%")
            ->whereHas('ecommerceProducts', fn ($query) => $query->where('status', 'in_stock'))
            ->orderBy('name')
            ->limit(4)
            ->get(['id', 'name'])
            ->map(fn ($brand) => [
                'type' => 'brand',
                'name' => $brand->name,
                'meta' => 'Brand',
                'url' => route('advanced', ['q' => $q, 'brand_id' => $brand->id]),
                'image' => null,
            ]);

        $categories = Category::query()
            ->where('name', 'like', "%{$q}%")
            ->whereHas('ecommerceProducts', fn ($query) => $query->where('status', 'in_stock'))
            ->orderBy('name')
            ->limit(4)
            ->get(['id', 'name'])
            ->map(fn ($category) => [
                'type' => 'category',
                'name' => $category->name,
                'meta' => 'Category',
                'url' => route('advanced', ['q' => $q, 'categories' => [$category->id]]),
                'image' => null,
            ]);

        $items = $productItems
            ->concat($brands)
            ->concat($categories)
            ->take(12)
            ->values();

        return response()->json(['items' => $items]);
    }
}
