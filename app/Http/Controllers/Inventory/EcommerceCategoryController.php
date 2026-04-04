<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\EcommerceProduct;
use Illuminate\Http\Request;

class EcommerceCategoryController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->input('search', ''));

        // Get categories that have at least one e-commerce product
        $categories = Category::query()
            ->whereHas('ecommerceProducts')
            ->withCount(['ecommerceProducts'])
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%$q%");
            })
            ->orderBy('order')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        // Get summary stats
        $totalCategories = Category::whereHas('ecommerceProducts')->count();
        $totalProducts = EcommerceProduct::count();

        return view('frontend.categories.index', [
            'categories' => $categories,
            'q' => $q,
            'totalCategories' => $totalCategories,
            'totalProducts' => $totalProducts,
        ]);
    }

    public function show(Category $category)
    {
        // Get all e-commerce products for this category
        $products = EcommerceProduct::query()
            ->whereHas('product', function ($query) use ($category) {
                $query->where('category_id', $category->id);
            })
            ->with(['product.brandRelation', 'images'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Count in-stock products
        $inStock = EcommerceProduct::query()
            ->whereHas('product', fn($q) => $q->where('category_id', $category->id))
            ->where('ecommerce_stock', '>', 0)
            ->count();

        return view('frontend.categories.show', [
            'category' => $category,
            'products' => $products,
            'inStock' => $inStock,
        ]);
    }
}
