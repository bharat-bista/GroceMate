<?php

// app/Http/Controllers/Inventory/ProductController.php
namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
  public function index(Request $request)
  {
    $q = trim($request->input('search', $request->input('q', '')));

    $products = Product::query()
      ->with(['category','stock','brandRelation'])
      ->when($q, fn($qq) => $qq->where('name','like',"%$q%")->orWhere('sku','like',"%$q%"))
      ->orderBy('name')
      ->orderBy('id')
      ->paginate(10)
      ->withQueryString();

    return view('inventory.products.index', [
      'products' => $products,
      'q' => $q,
    ]);
  }

  public function create()
  {
    return view('inventory.products.create', [
      'categories' => Category::orderBy('name')->get(),
      'brands' => Brand::orderBy('name')->get(),
      'units' => ['kg', 'liter', 'pcs', 'cartoon', 'peti', 'bori', 'box', 'bottle', 'pack', 'set'],
    ]);
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'category_id' => ['required','exists:categories,id'],
      'name' => ['required','string','max:255'],
      'brand_id' => ['nullable','exists:brands,id'],
      'brand_name' => ['nullable','string','max:255'],
      'sku' => ['nullable','string','max:50','unique:products,sku'],
      'unit' => ['required', 'in:kg,liter,pcs,cartoon,peti,bori,box,bottle,pack,set'],
      'selling_price' => ['required','numeric','min:0'],
      'description' => ['nullable','string'],
      'image_url' => ['nullable','string','max:2048'],
      'is_active' => ['nullable','boolean'],
      'is_listed' => ['nullable','boolean'],
      'reorder_level' => ['nullable','numeric','min:0'],
      'quantity' => ['nullable','numeric','min:0'],
    ]);

    DB::transaction(function () use ($data) {
      // Handle brand - use existing ID or create from name
      $brandId = null;
      if (!empty($data['brand_id'])) {
          $brandId = $data['brand_id'];
      } elseif (!empty($data['brand_name'])) {
          // Create new brand from name or find existing
          $brand = Brand::firstOrCreate(
              ['name' => trim($data['brand_name'])],
              ['name' => trim($data['brand_name'])]
          );
          $brandId = $brand->id;
      }

      $product = Product::create([
        'category_id' => $data['category_id'],
        'name' => $data['name'],
        'brand_id' => $brandId,
        'sku' => $data['sku'] ?? null,
        'unit' => $data['unit'],
        'selling_price' => $data['selling_price'],
        'description' => $data['description'] ?? null,
        'image_url' => $data['image_url'] ?? null,
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
      'units' => ['kg', 'liter', 'pcs', 'cartoon', 'peti', 'bori', 'box', 'bottle', 'pack', 'set'],
    ]);
  }

  public function update(Request $request, Product $product)
  {
    $data = $request->validate([
      'category_id' => ['required','exists:categories,id'],
      'name' => ['required','string','max:255'],
      'brand_id' => ['nullable','exists:brands,id'],
      'brand_name' => ['nullable','string','max:255'],
      'sku' => ['nullable','string','max:50','unique:products,sku,'.$product->id],
      'unit' => ['required','in:kg,liter,pcs'],
      'selling_price' => ['required','numeric','min:0'],
      'description' => ['nullable','string'],
      'image_url' => ['nullable','string','max:2048'],
      'is_active' => ['nullable','boolean'],
      'is_listed' => ['nullable','boolean'],
      'reorder_level' => ['nullable','numeric','min:0'],
    ]);

    DB::transaction(function () use ($data, $product) {
      // Handle brand - use existing ID or create from name
      $brandId = null;
      if (!empty($data['brand_id'])) {
          $brandId = $data['brand_id'];
      } elseif (!empty($data['brand_name'])) {
          // Create new brand from name or find existing
          $brand = Brand::firstOrCreate(
              ['name' => trim($data['brand_name'])],
              ['name' => trim($data['brand_name'])]
          );
          $brandId = $brand->id;
      }

      $product->update([
        'category_id' => $data['category_id'],
        'name' => $data['name'],
        'brand_id' => $brandId,
        'sku' => $data['sku'] ?? null,
        'unit' => $data['unit'],
        'selling_price' => $data['selling_price'],
        'description' => $data['description'] ?? null,
        'image_url' => $data['image_url'] ?? null,
        'is_active' => (bool)($data['is_active'] ?? false),
        'is_listed' => (bool)($data['is_listed'] ?? false),
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
}
