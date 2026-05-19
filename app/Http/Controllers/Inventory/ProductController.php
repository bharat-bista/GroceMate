<?php

// app/Http/Controllers/Inventory/ProductController.php
namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockBatch;
use App\Models\Brand;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
  public function index(Request $request)
  {
    $q = trim($request->input('search', $request->input('q', '')));
    $businessId = $request->input('business_id');

    $products = Product::query()
      ->with(['category','stock','brandRelation','business','ecommerceProduct','latestPurchaseItem'])
      ->when($q, fn($qq) => $qq->where('name','like',"%$q%"))
      ->when($businessId, fn($qq) => $qq->where('business_id', $businessId))
      ->orderBy('name')
      ->orderBy('id')
      ->paginate(10)
      ->withQueryString();

    return view('inventory.products.index', [
      'products' => $products,
      'q' => $q,
      'businesses' => Business::orderBy('business_name')->get(),
      'selectedBusinessId' => $businessId,
    ]);
  }

  public function batches(Request $request, Product $product)
  {
    $status = $request->get('status');
    $allowedStatuses = ['active', 'depleted', 'expired'];

    $batches = StockBatch::query()
      ->where('product_id', $product->id)
      ->when(in_array($status, $allowedStatuses, true), fn ($q) => $q->where('status', $status))
      ->orderBy('purchased_on')
      ->orderBy('id')
      ->paginate(10)
      ->withQueryString();

    $valuationTotal = (float) StockBatch::active()
      ->where('product_id', $product->id)
      ->selectRaw('SUM(qty_remaining * unit_cost) as total')
      ->value('total');

    return view('inventory.stock.batches', [
      'product' => $product,
      'batches' => $batches,
      'status' => $status,
      'valuationTotal' => $valuationTotal,
    ]);
  }

  public function create()
  {
    return view('inventory.products.create', [
      'categories' => Category::orderBy('name')->get(),
      'brands' => Brand::orderBy('name')->get(),
      'businesses' => Business::orderBy('business_name')->get(),
      'units' => ['kg', 'liter', 'pcs', 'cartoon', 'peti', 'bori', 'box', 'bottle', 'pack', 'set'],
    ]);
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'business_id' => ['required','exists:businesses,id'],
      'category_id' => ['required','exists:categories,id'],
      'name' => ['required','string','max:255'],
      'brand_id' => ['nullable','exists:brands,id'],
      'brand_name' => ['nullable','string','max:255'],
      'unit' => ['required', 'in:kg,liter,pcs,cartoon,peti,bori,box,bottle,pack,set'],
      'selling_price' => ['required','integer','min:0','max:9999999'],
      'is_active' => ['nullable','boolean'],
      'is_listed' => ['nullable','boolean'],
      'reorder_level' => ['nullable','numeric','min:0'],
      'quantity' => ['nullable','numeric','min:0'],
    ]);

    DB::transaction(function () use ($data) {
      $normalizedName = $this->normalizeProductName($data['name']);
      $brandId = $this->resolveBrandId($data);

      $this->assertNoCrossBusinessConflict($normalizedName, $brandId, (int) $data['business_id']);
      $this->assertNoDuplicateInsideBusiness($normalizedName, $brandId, (int) $data['business_id']);

      $product = Product::create([
        'business_id' => $data['business_id'],
        'category_id' => $data['category_id'],
        'name' => $normalizedName,
        'brand_id' => $brandId,
        'unit' => $data['unit'],
        'selling_price' => $data['selling_price'],
        'is_active' => (bool)($data['is_active'] ?? true),
        'is_listed' => (bool)($data['is_listed'] ?? false),
      ]);

      Stock::create([
        'product_id' => $product->id,
        'quantity' => $data['quantity'] ?? 0,
        'reorder_level' => $data['reorder_level'] ?? 0,
      ]);
    });

    return redirect()->route('inventory.products.index')->with('success', 'Product created.');
  }

  public function edit(Product $product)
  {
    $product->load('stock');
    return view('inventory.products.edit', [
      'product' => $product,
      'categories' => Category::orderBy('name')->get(),
      'brands' => Brand::orderBy('name')->get(),
      'businesses' => Business::orderBy('business_name')->get(),
      'units' => ['kg', 'liter', 'pcs', 'cartoon', 'peti', 'bori', 'box', 'bottle', 'pack', 'set'],
    ]);
  }

  public function update(Request $request, Product $product)
  {
    $data = $request->validate([
      'business_id' => ['required','exists:businesses,id'],
      'category_id' => ['required','exists:categories,id'],
      'name' => ['required','string','max:255'],
      'brand_id' => ['nullable','exists:brands,id'],
      'brand_name' => ['nullable','string','max:255'],
      'unit' => ['required','in:kg,liter,pcs,cartoon,peti,bori,box,bottle,pack,set'],
      'selling_price' => ['required','integer','min:0','max:9999999'],
      'is_active' => ['nullable','boolean'],
      'reorder_level' => ['nullable','numeric','min:0'],
    ]);

    DB::transaction(function () use ($data, $product) {
      $normalizedName = $this->normalizeProductName($data['name']);
      $brandId = $this->resolveBrandId($data);

      $this->assertNoCrossBusinessConflict(
        $normalizedName,
        $brandId,
        (int) $data['business_id'],
        $product->id
      );

      $this->assertNoDuplicateInsideBusiness(
        $normalizedName,
        $brandId,
        (int) $data['business_id'],
        $product->id
      );

      $product->update([
        'business_id' => $data['business_id'],
        'category_id' => $data['category_id'],
        'name' => $normalizedName,
        'brand_id' => $brandId,
        'unit' => $data['unit'],
        'selling_price' => $data['selling_price'],
        'is_active' => (bool)($data['is_active'] ?? false),
      ]);

      $product->stock()->updateOrCreate(
        ['product_id' => $product->id],
        ['reorder_level' => $data['reorder_level'] ?? 0]
      );
    });

    return redirect()->route('inventory.products.index')->with('success', 'Product updated.');
  }

  public function toggleListed(Product $product)
  {
    $product->update(['is_listed' => !$product->is_listed]);
    return back()->with('success', 'E-commerce listing updated.');
  }

  public function destroy(Product $product)
  {
    $qty = (float) ($product->stock->quantity ?? 0);
    if ($qty > 0) {
      return back()->with('error', 'Cannot delete: product still has stock. Deplete all stock first.');
    }

    DB::transaction(function () use ($product) {
      StockBatch::where('product_id', $product->id)->delete();
      Stock::where('product_id', $product->id)->delete();
      $product->ecommerceProduct?->delete();
      $product->delete();
    });

    return redirect()->route('inventory.products.index')
      ->with('success', "Product \"{$product->name}\" deleted.");
  }

  private function normalizeProductName(string $name): string
  {
    return trim(preg_replace('/\s+/', ' ', $name));
  }

  private function resolveBrandId(array $data): ?int
  {
    if (!empty($data['brand_id'])) {
      return (int) $data['brand_id'];
    }

    if (!empty($data['brand_name'])) {
      $brandName = trim($data['brand_name']);
      if ($brandName === '') {
        return null;
      }

      $brand = Brand::firstOrCreate(
        ['name' => $brandName],
        ['name' => $brandName]
      );

      return $brand->id;
    }

    return null;
  }

  private function assertNoCrossBusinessConflict(
    string $normalizedName,
    ?int $brandId,
    int $businessId,
    ?int $ignoreProductId = null
  ): void {
    $query = Product::query()
      ->with('business')
      ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($normalizedName)])
      ->where('business_id', '!=', $businessId)
      ->when($ignoreProductId, fn($q) => $q->where('id', '!=', $ignoreProductId));

    if (is_null($brandId)) {
      $query->whereNull('brand_id');
    } else {
      $query->where('brand_id', $brandId);
    }

    $conflict = $query->first();

    if ($conflict) {
      $owner = $conflict->business->business_name ?? 'another business';
      throw ValidationException::withMessages([
        'name' => "This product with the same brand/company already belongs to {$owner}. Choose a different company (brand) or product name.",
      ]);
    }
  }

  private function assertNoDuplicateInsideBusiness(
    string $normalizedName,
    ?int $brandId,
    int $businessId,
    ?int $ignoreProductId = null
  ): void {
    $query = Product::query()
      ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($normalizedName)])
      ->where('business_id', $businessId)
      ->when($ignoreProductId, fn($q) => $q->where('id', '!=', $ignoreProductId));

    if (is_null($brandId)) {
      $query->whereNull('brand_id');
    } else {
      $query->where('brand_id', $brandId);
    }

    if ($query->exists()) {
      throw ValidationException::withMessages([
        'name' => 'This business already has the same product name with the same brand/company.',
      ]);
    }
  }
}
