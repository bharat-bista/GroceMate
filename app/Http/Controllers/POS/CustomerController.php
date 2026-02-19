<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\POS\Customer;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers with search functionality.
     */
    public function index(Request $request)
    {
        $q = $request->input('q', '');

        $customers = Customer::where('name', 'like', "%$q%")
            ->orWhere('phone', 'like', "%$q%")
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('pos.customers.index', compact('customers', 'q'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('pos.customers.create');
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'vat_number' => 'nullable|string|max:50',
            'customer_type' => 'required|in:retail,wholesale,regular',
            'opening_due' => 'nullable|numeric|min:0', // Optional starting debt
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Initialize total_due from opening_due
        $validated['total_due'] = $validated['opening_due'] ?? 0;

        Customer::create($validated);

        return redirect()->route('pos.customers.index')
                         ->with('success', 'Customer added successfully.');
    }

    /**
     * Show the form for editing a customer.
     */
    public function edit(Customer $customer)
    {
        return view('pos.customers.edit', compact('customer'));
    }

    /**
     * Update a customer.
     * Note: total_due should not be manually edited here
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'vat_number' => 'nullable|string|max:50',
            'customer_type' => 'required|in:retail,wholesale,regular',
            'opening_due' => 'nullable|numeric|min:0', // optional edit
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Optionally allow updating opening_due if needed
        if(isset($validated['opening_due'])) {
            $customer->opening_due = $validated['opening_due'];
        }

        $customer->update($validated);

        return redirect()->route('pos.customers.index')
                         ->with('success', 'Customer updated successfully.');
    }

    /**
     * Delete a customer.
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('pos.customers.index')
                         ->with('success', 'Customer deleted successfully.');
    }
}
