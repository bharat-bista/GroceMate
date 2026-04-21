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
            ->storefrontVisible()
            ->whereHas('product', function ($query) {
                $query->whereNotNull('category_id')
                    ->whereNotNull('brand_id');
            });

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
        // Accept category arrays from multiple query formats:
        // categories=1&categories=2, categories[]=1, categories[0]=1, etc.
        $rawCategoryIds = $request->input('categories', []);
        if (empty($rawCategoryIds)) {
            $rawCategoryIds = $request->input('categories[]', []);
        }

        $categoryIds = collect((array) $rawCategoryIds)
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
            ->select([
                'id',
                'product_id',
                'discount_percent',
                'previous_price',
                'mrp',
                'display_price',
                'thumbnail',
                'created_at',
            ])
            ->with([
                'product:id,name,brand_id,category_id',
                'product.category:id,name',
                'product.brandRelation:id,name',
            ])
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
            ->whereHas('ecommerceProducts', fn ($query) => $query->storefrontVisible())
            ->orderBy('name')
            ->get(['id', 'name']);

        $categories = Category::query()
            ->whereHas('ecommerceProducts', fn ($query) => $query->storefrontVisible())
            ->withCount(['ecommerceProducts' => fn ($query) => $query->storefrontVisible()])
            ->orderBy('name')
            ->get(['id', 'name']);

        // For AJAX requests, return only the refreshed product result HTML
        // plus summary metadata used by the frontend.
        if ($request->ajax()) {
            return response()->json([
                'html' => view('frontend.advanced.partials.product-results', compact('ecommerceProducts'))->render(),
                'total' => $ecommerceProducts->total(),
                'query' => $request->query(),
            ]);
        }

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
            ->storefrontVisible()
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
            ->whereHas('ecommerceProducts', fn ($query) => $query->storefrontVisible())
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
            ->whereHas('ecommerceProducts', fn ($query) => $query->storefrontVisible())
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
