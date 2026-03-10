<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $suppliers = Supplier::query()
            ->when($q, function ($qq) use ($q) {
                $qq->where('name','like',"%{$q}%")
                   ->orWhere('phone','like',"%{$q}%")
                   ->orWhere('email','like',"%{$q}%");
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('inventory.suppliers.index', compact('suppliers','q'));
    }

    public function create()
    {
        return view('inventory.suppliers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'phone' => ['nullable','string','max:50'],
            'email' => ['nullable','email','max:255'],
            'vat_number' => ['nullable','string','max:50'],
            'pan_number' => ['nullable','string','max:50'],
            'supplier_type' => ['required','in:retail,wholesale,regular'],
            'opening_due' => ['nullable','numeric','min:0'],
            'address' => ['nullable','string','max:2000'],
        ]);

        // Set default values if not provided
        $data['supplier_type'] = $data['supplier_type'] ?? 'retail';
        $data['opening_due'] = $data['opening_due'] ?? 0;
        $data['total_due'] = $data['opening_due']; // Set total_due to opening_due initially

        Supplier::create($data);

        return redirect()->route('inventory.suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier)
    {
        return view('inventory.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'phone' => ['nullable','string','max:50'],
            'email' => ['nullable','email','max:255'],
            'vat_number' => ['nullable','string','max:50'],
            'pan_number' => ['nullable','string','max:50'],
            'supplier_type' => ['required','in:retail,wholesale,regular'],
            'opening_due' => ['nullable','numeric','min:0'],
            'address' => ['nullable','string','max:2000'],
        ]);

        // Set default values if not provided
        $data['supplier_type'] = $data['supplier_type'] ?? 'retail';
        $data['opening_due'] = $data['opening_due'] ?? 0;
        
        // Update total_due when opening_due changes
        $data['total_due'] = $data['opening_due'];

        $supplier->update($data);

        return redirect()->route('inventory.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier)
    {
        // When you add purchases later, you can block delete like this:
        // if ($supplier->purchases()->exists()) { ... }

        $supplier->delete();

        return redirect()->route('inventory.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
