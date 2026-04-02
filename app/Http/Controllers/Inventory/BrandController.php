<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $brands = Brand::query()
            ->when($q, fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->orderBy('order')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('inventory.brands.index', compact('brands', 'q'));
    }

    public function create()
    {
        return view('inventory.brands.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:brands,name'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'order' => ['required', 'integer', 'min:0'],
            'company_discount' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/img/brands'), $imageName);
            $data['image'] = $imageName;
        }

        Brand::create($data);

        return redirect()
            ->route('inventory.brands.index')
            ->with('success', 'Brand created successfully.');
    }

    public function edit(Brand $brand)
    {
        return view('inventory.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:brands,name,' . $brand->id],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'order' => ['required', 'integer', 'min:0'],
            'company_discount' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($brand->image && file_exists(public_path('assets/img/brands/' . $brand->image))) {
                unlink(public_path('assets/img/brands/' . $brand->image));
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/img/brands'), $imageName);
            $data['image'] = $imageName;
        }

        $brand->update($data);

        return redirect()
            ->route('inventory.brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand)
    {
        // Prevent delete if products exist
        if ($brand->products()->exists()) {
            return back()->with('success', 'Cannot delete: brand has products. (Disable products or move them first)');
        }

        // Delete image if exists
        if ($brand->image && file_exists(public_path('assets/img/brands/' . $brand->image))) {
            unlink(public_path('assets/img/brands/' . $brand->image));
        }

        $brand->delete();

        return redirect()
            ->route('inventory.brands.index')
            ->with('success', 'Brand deleted successfully.');
    }
}
