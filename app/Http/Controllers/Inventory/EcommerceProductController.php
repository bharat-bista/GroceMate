<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\EcommerceProduct;
use App\Models\EcommerceProductImage;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class EcommerceProductController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->input('search', ''));

        $ecommerceProducts = EcommerceProduct::query()
            ->with(['product.category', 'product.brandRelation', 'product.latestPurchaseItem', 'product.stock', 'images'])
            ->when($q, function ($query) use ($q) {
                $query->whereHas('product', function ($q2) use ($q) {
                    $q2->where('name', 'like', "%$q%");
                })->orWhere('sku', 'like', "%$q%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('frontend.product.index', [
            'ecommerceProducts' => $ecommerceProducts,
            'q' => $q,
        ]);
    }

    public function create()
    {
        // Get all products that don't have ecommerce product yet
        $products = Product::whereDoesntHave('ecommerceProduct')
            ->with(['category', 'brandRelation', 'latestPurchaseItem', 'stock'])
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        return view('frontend.product.create', [
            'products' => $products,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required', 'exists:products,id', 'unique:ecommerce_products,product_id'],
            'sku' => ['nullable', 'string', 'max:50', 'unique:ecommerce_products,sku'],
            'status' => ['required', 'in:in_stock,out_of_stock,coming_soon'],
            'ecommerce_stock' => ['nullable', 'numeric', 'min:0'],
            'mrp' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
        ]);

        // Calculate display price
        $discountPercent = $data['discount_percent'] ?? 0;
        $mrp = $data['mrp'];
        $displayPrice = $mrp - ($mrp * $discountPercent / 100);

        // Get purchase price for profit calculation
        $product = Product::with(['latestPurchaseItem', 'stock'])->find($data['product_id']);
        $purchasePrice = $product->latestPurchaseItem->unit_cost ?? 0;
        $profit = $displayPrice - $purchasePrice;

        // Validate stock availability
        $requestedStock = $data['ecommerce_stock'] ?? 0;
        $availableStock = $product->stock->quantity ?? 0;

        if ($requestedStock > $availableStock) {
            return back()->withInput()->withErrors([
                'ecommerce_stock' => "Insufficient inventory stock. Available: {$availableStock}"
            ]);
        }

        DB::transaction(function () use ($data, $mrp, $discountPercent, $displayPrice, $profit, $request, $product, $requestedStock) {
            $ecommerceProduct = EcommerceProduct::create([
                'product_id' => $data['product_id'],
                'sku' => $data['sku'],
                'status' => $data['status'],
                'ecommerce_stock' => $requestedStock,
                'mrp' => $mrp,
                'discount_percent' => $discountPercent,
                'display_price' => $displayPrice,
                'profit' => $profit,
                'meta_keywords' => $data['meta_keywords'],
                'description' => $data['description'],
                'thumbnail' => null,
            ]);

            // Deduct stock from inventory if e-commerce stock is set
            if ($requestedStock > 0 && $product->stock) {
                $product->stock->decrement('quantity', $requestedStock);
            }

            // Handle multiple images upload
            if ($request->hasFile('images')) {
                $sortOrder = 0;
                foreach ($request->file('images') as $image) {
                    $path = $image->store('ecommerce-products', 'public');
                    EcommerceProductImage::create([
                        'ecommerce_product_id' => $ecommerceProduct->id,
                        'image_path' => $path,
                        'sort_order' => $sortOrder,
                        'is_primary' => $sortOrder === 0,
                    ]);
                    $sortOrder++;
                }

                // Set first image as thumbnail
                $firstImage = $ecommerceProduct->images()->first();
                if ($firstImage) {
                    $ecommerceProduct->update(['thumbnail' => $firstImage->image_path]);
                }
            }
        });

        return redirect()->route('inventory.ecommerce-products.index')
            ->with('success', 'E-commerce product created successfully.');
    }

    public function edit(EcommerceProduct $ecommerceProduct)
    {
        $ecommerceProduct->load(['product.category', 'product.brandRelation', 'product.latestPurchaseItem', 'product.stock', 'images']);

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
            'ecommerce_stock' => ['nullable', 'numeric', 'min:0'],
            'mrp' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
            'delete_images' => ['nullable', 'array'],
            'delete_images.*' => ['integer', 'exists:ecommerce_product_images,id'],
            'primary_image' => ['nullable', 'integer', 'exists:ecommerce_product_images,id'],
        ]);

        // Calculate display price
        $discountPercent = $data['discount_percent'] ?? 0;
        $mrp = $data['mrp'];
        $displayPrice = $mrp - ($mrp * $discountPercent / 100);

        // Get purchase price for profit calculation
        $purchasePrice = $ecommerceProduct->product->latestPurchaseItem->unit_cost ?? 0;
        $profit = $displayPrice - $purchasePrice;

        // Calculate stock difference
        $newStock = $data['ecommerce_stock'] ?? 0;
        $currentStock = $ecommerceProduct->ecommerce_stock ?? 0;
        $stockDifference = $newStock - $currentStock;

        // Validate stock availability if increasing e-commerce stock
        if ($stockDifference > 0) {
            $availableStock = $ecommerceProduct->product->stock->quantity ?? 0;
            if ($stockDifference > $availableStock) {
                return back()->withInput()->withErrors([
                    'ecommerce_stock' => "Insufficient inventory stock. Available: {$availableStock}, Requested increase: {$stockDifference}"
                ]);
            }
        }

        DB::transaction(function () use ($data, $mrp, $discountPercent, $displayPrice, $profit, $request, $ecommerceProduct, $newStock, $stockDifference) {
            // Delete selected images
            if (!empty($data['delete_images'])) {
                $imagesToDelete = EcommerceProductImage::whereIn('id', $data['delete_images'])
                    ->where('ecommerce_product_id', $ecommerceProduct->id)
                    ->get();

                foreach ($imagesToDelete as $image) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }

            // Handle new images upload
            if ($request->hasFile('images')) {
                $maxSortOrder = $ecommerceProduct->images()->max('sort_order') ?? -1;
                foreach ($request->file('images') as $image) {
                    $maxSortOrder++;
                    $path = $image->store('ecommerce-products', 'public');
                    EcommerceProductImage::create([
                        'ecommerce_product_id' => $ecommerceProduct->id,
                        'image_path' => $path,
                        'sort_order' => $maxSortOrder,
                        'is_primary' => false,
                    ]);
                }
            }

            // Update primary image
            if (!empty($data['primary_image'])) {
                // Reset all to non-primary
                $ecommerceProduct->images()->update(['is_primary' => false]);
                // Set selected as primary
                EcommerceProductImage::where('id', $data['primary_image'])
                    ->where('ecommerce_product_id', $ecommerceProduct->id)
                    ->update(['is_primary' => true]);
            }

            // Update thumbnail to primary image or first image
            $primaryImage = $ecommerceProduct->images()->where('is_primary', true)->first();
            if (!$primaryImage) {
                $primaryImage = $ecommerceProduct->images()->orderBy('sort_order')->first();
                if ($primaryImage) {
                    $primaryImage->update(['is_primary' => true]);
                }
            }

            // Update inventory stock based on difference
            if ($stockDifference != 0 && $ecommerceProduct->product->stock) {
                if ($stockDifference > 0) {
                    // Increasing e-commerce stock - deduct from inventory
                    $ecommerceProduct->product->stock->decrement('quantity', $stockDifference);
                } else {
                    // Decreasing e-commerce stock - return to inventory
                    $ecommerceProduct->product->stock->increment('quantity', abs($stockDifference));
                }
            }

            $ecommerceProduct->update([
                'sku' => $data['sku'],
                'status' => $data['status'],
                'ecommerce_stock' => $newStock,
                'mrp' => $mrp,
                'discount_percent' => $discountPercent,
                'display_price' => $displayPrice,
                'profit' => $profit,
                'meta_keywords' => $data['meta_keywords'],
                'description' => $data['description'],
                'thumbnail' => $primaryImage ? $primaryImage->image_path : null,
            ]);
        });

        return redirect()->route('inventory.ecommerce-products.index')
            ->with('success', 'E-commerce product updated successfully.');
    }

    public function destroy(EcommerceProduct $ecommerceProduct)
    {
        // Delete all images
        foreach ($ecommerceProduct->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        // Delete thumbnail if exists separately
        if ($ecommerceProduct->thumbnail) {
            Storage::disk('public')->delete($ecommerceProduct->thumbnail);
        }

        $ecommerceProduct->delete();

        return redirect()->route('inventory.ecommerce-products.index')
            ->with('success', 'E-commerce product deleted successfully.');
    }

    // AJAX endpoint to delete a single image
    public function deleteImage(Request $request, EcommerceProduct $ecommerceProduct, EcommerceProductImage $image)
    {
        if ($image->ecommerce_product_id !== $ecommerceProduct->id) {
            return response()->json(['error' => 'Image not found'], 404);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        // Update thumbnail if deleted image was primary
        if ($image->is_primary) {
            $newPrimary = $ecommerceProduct->images()->orderBy('sort_order')->first();
            if ($newPrimary) {
                $newPrimary->update(['is_primary' => true]);
                $ecommerceProduct->update(['thumbnail' => $newPrimary->image_path]);
            } else {
                $ecommerceProduct->update(['thumbnail' => null]);
            }
        }

        return response()->json(['success' => true]);
    }

    // AJAX endpoint to set primary image
    public function setPrimaryImage(Request $request, EcommerceProduct $ecommerceProduct, EcommerceProductImage $image)
    {
        if ($image->ecommerce_product_id !== $ecommerceProduct->id) {
            return response()->json(['error' => 'Image not found'], 404);
        }

        $ecommerceProduct->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);
        $ecommerceProduct->update(['thumbnail' => $image->image_path]);

        return response()->json(['success' => true]);
    }
}
