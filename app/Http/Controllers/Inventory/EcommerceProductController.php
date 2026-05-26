<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\EcommerceProduct;
use App\Models\EcommerceProductImage;
use App\Models\Product;
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

        $title = 'E-commerce Products';
        $subtitle = 'Manage products for online store';

        return view('frontend.product.index', [
            'ecommerceProducts' => $ecommerceProducts,
            'q' => $q,
            'status' => $status,
            'title' => $title,
            'subtitle' => $subtitle,
            'businesses' => Business::orderBy('business_name')->get(),
            'selectedBusinessId' => $businessId,
        ]);
    }

    public function create(Request $request)
    {
        $businesses = Business::orderBy('business_name')->get();
        $businessId = $request->input('business_id') ?? $businesses->first()?->id;

        return view('frontend.product.create', [
            'businesses'         => $businesses,
            'selectedBusinessId' => $businessId,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_id'      => ['required', 'exists:businesses,id'],
            'product_id'       => ['required', 'exists:products,id'],
            'sku'              => ['nullable', 'string', 'max:50', 'unique:ecommerce_products,sku'],
            'mrp'              => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'ecommerce_stock'  => ['required', 'numeric', 'min:0'],
            'meta_keywords'    => ['nullable', 'string', 'max:500'],
            'description'      => ['nullable', 'string'],
            'thumbnails'       => ['nullable', 'array'],
            'thumbnails.*'     => ['image', 'max:2048'],
        ]);

        $product = Product::with(['latestPurchaseItem', 'stock'])
            ->where('id', $data['product_id'])
            ->where('business_id', $data['business_id'])
            ->first();

        if (!$product) {
            return back()
                ->withErrors(['product_id' => 'Selected product does not belong to the selected business account.'])
                ->withInput();
        }

        // Block if an active listing (ecommerce_stock > 0) already exists for this product.
        $existing = EcommerceProduct::where('product_id', $data['product_id'])->first();
        if ($existing && (float) $existing->ecommerce_stock > 0) {
            return back()
                ->withErrors(['product_id' => 'This product already has an active ecommerce listing. Edit it instead.'])
                ->withInput();
        }

        $totalStock    = (float) ($product->stock->quantity ?? 0);
        $reservedStock = (float) $data['ecommerce_stock'];

        if ($reservedStock > $totalStock) {
            return back()
                ->withErrors(['ecommerce_stock' => 'Ecommerce stock cannot be greater than the available inventory stock.'])
                ->withInput();
        }

        $discountPercent = $data['discount_percent'] ?? 0;
        $mrp             = $data['mrp'];
        $displayPrice    = round($mrp - ($mrp * $discountPercent / 100), 2);
        $purchasePrice   = $product->latestPurchaseItem->unit_cost ?? 0;
        $profit          = round($displayPrice - $purchasePrice, 2);

        $thumbnailPaths = [];
        if ($request->hasFile('thumbnails')) {
            foreach ($request->file('thumbnails') as $thumbnailFile) {
                if ($thumbnailFile) {
                    $thumbnailPaths[] = $thumbnailFile->store('ecommerce-products', 'public');
                }
            }
        }

        $payload = [
            'sku'              => $data['sku'],
            'status'           => $reservedStock > 0 ? 'in_stock' : 'out_of_stock',
            'display_section'  => 'product_grid',
            'mrp'              => $mrp,
            'discount_percent' => $discountPercent,
            'display_price'    => $displayPrice,
            'profit'           => $profit,
            'ecommerce_stock'  => $reservedStock,
            'meta_keywords'    => $data['meta_keywords'],
            'description'      => $data['description'],
        ];

        if (!empty($thumbnailPaths)) {
            $payload['thumbnail'] = $thumbnailPaths[0];
        }

        if ($existing) {
            // Re-listing a depleted product — update the existing row (DB unique constraint).
            $existing->update($payload);
            $ecommerceProduct = $existing;
        } else {
            $payload['product_id'] = $data['product_id'];
            $ecommerceProduct = EcommerceProduct::create($payload);
        }

        $this->saveEcommerceImages($ecommerceProduct, $thumbnailPaths);

        return redirect()->route('inventory.ecommerce-products.index')
            ->with('success', 'E-commerce product created successfully.');
    }

    public function edit(EcommerceProduct $ecommerceProduct)
    {
        $ecommerceProduct->load(['product.category', 'product.brandRelation', 'product.latestPurchaseItem', 'product.stock', 'images' => function ($query) {
            $query->orderBy('is_primary', 'desc')->orderBy('sort_order')->orderBy('id');
        }]);

        return view('frontend.product.edit', [
            'ecommerceProduct' => $ecommerceProduct,
        ]);
    }

    public function update(Request $request, EcommerceProduct $ecommerceProduct)
    {
        $data = $request->validate([
            'sku' => ['nullable', 'string', 'max:50', 'unique:ecommerce_products,sku,' . $ecommerceProduct->id],
            'mrp' => ['required', 'numeric', 'min:0'],
            'discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'ecommerce_stock' => ['required', 'numeric', 'min:0'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'thumbnails' => ['nullable', 'array'],
            'thumbnails.*' => ['image', 'max:2048'],
        ]);

        $discountPercent = $data['discount_percent'] ?? 0;
        $mrp = $data['mrp'];
        $displayPrice = round($mrp - ($mrp * $discountPercent / 100), 2);
        $purchasePrice = $ecommerceProduct->product->latestPurchaseItem->unit_cost ?? 0;
        $profit = round($displayPrice - $purchasePrice, 2);

        $totalStock = (float) ($ecommerceProduct->product->stock->quantity ?? 0);
        $reservedStock = (float) $data['ecommerce_stock'];

        if ($reservedStock > $totalStock) {
            return back()
                ->withErrors(['ecommerce_stock' => 'Ecommerce stock cannot be greater than the available inventory stock.'])
                ->withInput();
        }

        // Handle thumbnail uploads
        $thumbnailPaths = [];
        if ($request->hasFile('thumbnails')) {
            foreach ($request->file('thumbnails') as $thumbnailFile) {
                if ($thumbnailFile) {
                    $thumbnailPaths[] = $thumbnailFile->store('ecommerce-products', 'public');
                }
            }
        }

        $thumbnailPath = $ecommerceProduct->thumbnail;
        if (!empty($thumbnailPaths)) {
            $thumbnailPath = $thumbnailPaths[0];
        }

        $status = $reservedStock > 0 ? 'in_stock' : 'out_of_stock';

        $ecommerceProduct->update([
            'sku' => $data['sku'],
            'status' => $status,
            'display_section' => 'product_grid',
            'mrp' => $mrp,
            'discount_percent' => $discountPercent,
            'display_price' => $displayPrice,
            'profit' => $profit,
            'ecommerce_stock' => $reservedStock,
            'meta_keywords' => $data['meta_keywords'],
            'description' => $data['description'],
            'thumbnail' => $thumbnailPath,
        ]);

        if (!empty($thumbnailPaths)) {
            $this->saveEcommerceImages($ecommerceProduct, $thumbnailPaths);
        }

        return redirect()->route('inventory.ecommerce-products.index')
            ->with('success', 'E-commerce product updated successfully.');
    }

    public function destroy(EcommerceProduct $ecommerceProduct)
    {
        $ecommerceProduct->load('images');

        // Delete thumbnail files and saved ecommerce image records.
        if ($ecommerceProduct->thumbnail) {
            Storage::disk('public')->delete($ecommerceProduct->thumbnail);
        }

        foreach ($ecommerceProduct->images as $image) {
            if ($image->image_path) {
                Storage::disk('public')->delete($image->image_path);
            }
        }

        $ecommerceProduct->delete();

        return redirect()->route('inventory.ecommerce-products.index')
            ->with('success', 'E-commerce product deleted successfully.');
    }

    public function deleteThumbnail(EcommerceProduct $ecommerceProduct)
    {
        if ($ecommerceProduct->thumbnail) {
            Storage::disk('public')->delete($ecommerceProduct->thumbnail);
            $ecommerceProduct->update(['thumbnail' => null]);
        }

        return back()->with('success', 'Main thumbnail removed.');
    }

    public function deleteImage(EcommerceProduct $ecommerceProduct, EcommerceProductImage $image)
    {
        if ($image->ecommerce_product_id !== $ecommerceProduct->id) {
            abort(403);
        }

        if ($image->image_path) {
            Storage::disk('public')->delete($image->image_path);
        }

        $wasPrimary = $image->is_primary;
        $image->delete();

        if ($wasPrimary) {
            $ecommerceProduct->images()->orderBy('sort_order')->first()?->update(['is_primary' => true]);
        }

        return back()->with('success', 'Image removed.');
    }

    private function saveEcommerceImages(EcommerceProduct $ecommerceProduct, array $imagePaths): void
    {
        if (empty($imagePaths)) {
            return;
        }

        $existingCount = $ecommerceProduct->images()->count();
        $nextSortOrder = (int) $ecommerceProduct->images()->max('sort_order');
        $isFirstImage = $existingCount === 0;

        foreach ($imagePaths as $index => $imagePath) {
            EcommerceProductImage::create([
                'ecommerce_product_id' => $ecommerceProduct->id,
                'image_path' => $imagePath,
                'sort_order' => $nextSortOrder + $index + 1,
                'is_primary' => $isFirstImage && $index === 0,
            ]);
        }
    }
}
