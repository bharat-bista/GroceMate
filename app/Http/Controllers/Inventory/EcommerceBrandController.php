<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\EcommerceProduct;
use Illuminate\Http\Request;

class EcommerceBrandController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->input('search', ''));

        // Get brands that have at least one e-commerce product
        $brands = Brand::query()
            ->whereHas('ecommerceProducts')
            ->withCount(['ecommerceProducts'])
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%$q%");
            })
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        // Get summary stats
        $totalBrands = Brand::whereHas('ecommerceProducts')->count();
        $totalProducts = EcommerceProduct::count();

        return view('frontend.brand.index', [
            'brands' => $brands,
            'q' => $q,
            'totalBrands' => $totalBrands,
            'totalProducts' => $totalProducts,
        ]);
    }

    public function show(Brand $brand)
    {
        // Get all e-commerce products for this brand
        $products = EcommerceProduct::query()
            ->whereHas('product', function ($query) use ($brand) {
                $query->where('brand_id', $brand->id);
            })
            ->with(['product.category', 'images'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Count in-stock products
        $inStock = EcommerceProduct::query()
            ->whereHas('product', fn($q) => $q->where('brand_id', $brand->id))
            ->where('ecommerce_stock', '>', 0)
            ->count();

        return view('frontend.brand.show', [
            'brand' => $brand,
            'products' => $products,
            'inStock' => $inStock,
        ]);
    }
}
