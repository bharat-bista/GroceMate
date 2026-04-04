<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\EcommerceProduct;
use App\Models\Product;
use App\Models\Category;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EcommerceProductController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->input('search', ''));
        $status = trim((string) $request->input('status', ''));
        $businessId = $request->input('business_id');

        $ecommerceProducts = EcommerceProduct::query()
            ->with(['product.category', 'product.brandRelation', 'product.latestPurchaseItem', 'product.business'])
            ->when($q, function ($query) use ($q) {
                $query->whereHas('product', function ($q2) use ($q) {
                    $q2->where('name', 'like', "%$q%");
                })->orWhere('sku', 'like', "%$q%");
            })
            ->when($status !== '', fn($query) => $query->where('status', $status))
            ->when($businessId, function ($query) use ($businessId) {
                $query->whereHas('product', fn($q2) => $q2->where('business_id', $businessId));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('frontend.product.index', [
            'ecommerceProducts' => $ecommerceProducts,
            'q' => $q,
            'status' => $status,
            'businesses' => Business::orderBy('business_name')->get(),
            'selectedBusinessId' => $businessId,
        ]);
    }

    public function create(Request $request)
    {
        $businessId = $request->input('business_id');

        // Get products that are marked as listed for e-commerce but don't have ecommerce product yet
        $products = Product::where('is_listed', true)
            ->whereDoesntHave('ecommerceProduct')
            ->when($businessId, fn($query) => $query->where('business_id', $businessId))
            ->with(['category', 'brandRelation', 'latestPurchaseItem'])
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('frontend.product.create', [
            'products' => $products,
            'categories' => $categories,
            'businesses' => Business::orderBy('business_name')->get(),
            'selectedBusinessId' => $businessId,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id', 'unique:ecommerce_products,product_id'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:ecommerce_products,sku'],
            'status' => ['required', 'in:in_stock,out_of_stock,coming_soon'],
            'previous_price' => ['nullable', 'numeric', 'min:0'],
            'mrp' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        // Calculate display price
        $discountPercent = $data['discount_percent'] ?? 0;
        $mrp = $data['mrp'];
        $displayPrice = $mrp - ($mrp * $discountPercent / 100);

        // Get purchase price for profit calculation
        $product = Product::with('latestPurchaseItem')->find($data['product_id']);
        $purchasePrice = $product->latestPurchaseItem->unit_cost ?? 0;
        $profit = $displayPrice - $purchasePrice;

        // Handle thumbnail upload
        $thumbnailPath = null;
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('ecommerce-products', 'public');
        }

        EcommerceProduct::create([
            'product_id' => $data['product_id'],
            'sku' => $data['sku'],
            'status' => $data['status'],
            'previous_price' => $data['previous_price'],
            'mrp' => $mrp,
            'discount_percent' => $discountPercent,
            'display_price' => $displayPrice,
            'profit' => $profit,
            'meta_keywords' => $data['meta_keywords'],
            'description' => $data['description'],
            'thumbnail' => $thumbnailPath,
        ]);

        return redirect()->route('inventory.ecommerce-products.index')
            ->with('success', 'E-commerce product created successfully.');
    }

    public function edit(EcommerceProduct $ecommerceProduct)
    {
        $ecommerceProduct->load(['product.category', 'product.brandRelation', 'product.latestPurchaseItem']);

        $categories = Category::orderBy('name')->get();

        return view('frontend.product.edit', [
            'ecommerceProduct' => $ecommerceProduct,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, EcommerceProduct $ecommerceProduct)
    {
        $data = $request->validate([
            'sku' => ['nullable', 'string', 'max:50', 'unique:ecommerce_products,sku,' . $ecommerceProduct->id],
            'status' => ['required', 'in:in_stock,out_of_stock,coming_soon'],
            'previous_price' => ['nullable', 'numeric', 'min:0'],
            'mrp' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
        ]);

        // Calculate display price
        $discountPercent = $data['discount_percent'] ?? 0;
        $mrp = $data['mrp'];
        $displayPrice = $mrp - ($mrp * $discountPercent / 100);

        // Get purchase price for profit calculation
        $purchasePrice = $ecommerceProduct->product->latestPurchaseItem->unit_cost ?? 0;
        $profit = $displayPrice - $purchasePrice;

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($ecommerceProduct->thumbnail) {
                Storage::disk('public')->delete($ecommerceProduct->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('ecommerce-products', 'public');
        }

        $ecommerceProduct->update([
            'sku' => $data['sku'],
            'status' => $data['status'],
            'previous_price' => $data['previous_price'],
            'mrp' => $mrp,
            'discount_percent' => $discountPercent,
            'display_price' => $displayPrice,
            'profit' => $profit,
            'meta_keywords' => $data['meta_keywords'],
            'description' => $data['description'],
            'thumbnail' => $data['thumbnail'] ?? $ecommerceProduct->thumbnail,
        ]);

        return redirect()->route('inventory.ecommerce-products.index')
            ->with('success', 'E-commerce product updated successfully.');
    }

    public function destroy(EcommerceProduct $ecommerceProduct)
    {
        // Delete thumbnail
        if ($ecommerceProduct->thumbnail) {
            Storage::disk('public')->delete($ecommerceProduct->thumbnail);
        }

        $ecommerceProduct->delete();

        return redirect()->route('inventory.ecommerce-products.index')
            ->with('success', 'E-commerce product deleted successfully.');
    }
}
