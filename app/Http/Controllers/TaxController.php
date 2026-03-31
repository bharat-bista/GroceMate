<?php

namespace App\Http\Controllers;

use App\Models\Tax;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    /**
     * Display a listing of taxes.
     */
    public function index()
    {
        $taxes = Tax::latest()->get();
        return view('taxes.index', compact('taxes'));
    }

    /**
     * Show the form for creating a new tax.
     */
    public function create()
    {
        return view('taxes.create');
    }

    /**
     * Store a newly created tax.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:taxes,name',
            'type' => 'required|in:percentage,fixed',
            'rate' => 'required|numeric|min:0|max:100',
        ]);

        Tax::create($request->all());

        return redirect()->route('taxes.index')
            ->with('success', 'Tax created successfully.');
    }

    /**
     * Show the form for editing the specified tax.
     */
    public function edit(Tax $tax)
    {
        return view('taxes.edit', compact('tax'));
    }

    /**
     * Update the specified tax.
     */
    public function update(Request $request, Tax $tax)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:taxes,name,' . $tax->id,
            'type' => 'required|in:percentage,fixed',
            'rate' => 'required|numeric|min:0|max:100',
        ]);

        $tax->update($request->all());

        return redirect()->route('taxes.index')
            ->with('success', 'Tax updated successfully.');
    }

    /**
     * Remove the specified tax.
     */
    public function destroy(Tax $tax)
    {
        // Check if tax is being used in any purchases or invoices
        if ($tax->purchaseItems()->exists()) {
            return redirect()->route('taxes.index')
                ->with('error', 'Cannot delete tax. It is being used in transactions.');
        }

        $tax->delete();

        return redirect()->route('taxes.index')
            ->with('success', 'Tax deleted successfully.');
    }
}
