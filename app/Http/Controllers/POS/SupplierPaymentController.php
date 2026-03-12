<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\SupplierPayment;
use App\Models\Supplier;
use App\Models\Business;
use App\Models\POS\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = SupplierPayment::with('supplier')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('pos.payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $businesses = Business::all();
        $paymentMethods = ['cash', 'bank', 'Khalti'];
        
        return view('pos.payments.create', compact('suppliers', 'businesses', 'paymentMethods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'business_account' => 'nullable|exists:businesses,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:external,integrated',
            'payment_method_external' => 'required_if:payment_type,external|in:cash,bank,esewa_external,khalti_external',
            'payment_method_integrated' => 'required_if:payment_type,integrated|in:khalti_integrated',
            'payment_reference' => 'nullable|string|max:255',
            'bank_charge' => 'nullable|numeric|min:0',
            'tds_applicable' => 'boolean',
            'note' => 'nullable|string|max:1000',
        ]);

        $validated['bank_charge'] = $validated['bank_charge'] ?? 0;
        $validated['tds_applicable'] = $request->has('tds_applicable');

        // Determine the actual payment method based on payment type
        if ($validated['payment_type'] === 'external') {
            $paymentMethod = $validated['payment_method_external'];
        } else {
            $paymentMethod = $validated['payment_method_integrated'];
        }

        // Add the payment_method to validated data for database storage
        $validated['payment_method'] = $paymentMethod;

        // Use database transaction to ensure data integrity
        DB::transaction(function () use ($validated, $paymentMethod) {
            // Create the supplier payment
            $supplierPayment = SupplierPayment::create($validated);

            // 1. Decrease supplier due amount
            $supplier = Supplier::find($validated['supplier_id']);
            if ($supplier) {
                $supplier->decrement('total_due', $validated['amount']);
            }

            // 2. Decrease from business account balance (if selected)
            if (!empty($validated['business_account'])) {
                $business = Business::find($validated['business_account']);
                if ($business) {
                    $business->decrement('balance', $validated['amount']);
                }
            }

            // 3. Create a negative income entry (expense) to track the payment
            Income::create([
                'transaction_date' => $validated['date'],
                'customer_id' => null, // Not applicable for supplier payments
                'business_id' => $validated['business_account'],
                'amount_received' => -$validated['amount'], // Negative amount for expense
                'payment_method' => $paymentMethod,
                'reference_no' => 'PAY-' . $supplierPayment->id,
                'notes' => 'Supplier payment to ' . $supplier->name . ': ' . ($validated['note'] ?? '')
            ]);
        });

        return redirect()->route('pos.supplier-payments.index')
            ->with('success', 'Supplier payment created successfully and financial records updated.');
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierPayment $supplierPayment)
    {
        return view('pos.payments.show', compact('supplierPayment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierPayment $supplierPayment)
    {
        $suppliers = Supplier::all();
        $businesses = Business::all();
        $paymentMethods = ['cash', 'bank', 'Khalti'];
        
        return view('pos.payments.edit', compact('supplierPayment', 'suppliers', 'businesses', 'paymentMethods'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SupplierPayment $supplierPayment)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'business_account' => 'nullable|exists:businesses,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank,Khalti',
            'payment_reference' => 'nullable|string|max:255',
            'bank_charge' => 'nullable|numeric|min:0',
            'tds_applicable' => 'boolean',
            'note' => 'nullable|string|max:1000',
        ]);

        $validated['bank_charge'] = $validated['bank_charge'] ?? 0;
        $validated['tds_applicable'] = $request->has('tds_applicable');

        // Use database transaction to ensure data integrity
        DB::transaction(function () use ($validated, $supplierPayment) {
            // Get original payment details for reversal
            $originalAmount = $supplierPayment->amount;
            $originalSupplierId = $supplierPayment->supplier_id;
            $originalBusinessId = $supplierPayment->business_account;

            // 1. Reverse the original financial changes
            // Restore supplier due amount
            $originalSupplier = Supplier::find($originalSupplierId);
            if ($originalSupplier) {
                $originalSupplier->increment('total_due', $originalAmount);
            }

            // Restore business account balance
            if ($originalBusinessId) {
                $originalBusiness = Business::find($originalBusinessId);
                if ($originalBusiness) {
                    $originalBusiness->increment('balance', $originalAmount);
                }
            }

            // Delete the original income entry
            Income::where('reference_no', 'PAY-' . $supplierPayment->id)->delete();

            // Update the supplier payment
            $supplierPayment->update($validated);

            // 2. Apply new financial changes
            $supplier = Supplier::find($validated['supplier_id']);
            if ($supplier) {
                $supplier->decrement('total_due', $validated['amount']);
            }

            if (!empty($validated['business_account'])) {
                $business = Business::find($validated['business_account']);
                if ($business) {
                    $business->decrement('balance', $validated['amount']);
                }
            }

            // Create new income entry
            Income::create([
                'transaction_date' => $validated['date'],
                'customer_id' => null,
                'business_id' => $validated['business_account'],
                'amount_received' => -$validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_no' => 'PAY-' . $supplierPayment->id,
                'notes' => 'Supplier payment to ' . $supplier->name . ': ' . ($validated['note'] ?? '')
            ]);
        });

        return redirect()->route('pos.supplier-payments.index')
            ->with('success', 'Supplier payment updated successfully and financial records updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierPayment $supplierPayment)
    {
        // Use database transaction to ensure data integrity
        DB::transaction(function () use ($supplierPayment) {
            // 1. Restore supplier due amount
            $supplier = Supplier::find($supplierPayment->supplier_id);
            if ($supplier) {
                $supplier->increment('total_due', $supplierPayment->amount);
            }

            // 2. Restore business account balance
            if ($supplierPayment->business_account) {
                $business = Business::find($supplierPayment->business_account);
                if ($business) {
                    $business->increment('balance', $supplierPayment->amount);
                }
            }

            // 3. Delete the income entry
            Income::where('reference_no', 'PAY-' . $supplierPayment->id)->delete();

            // 4. Delete the supplier payment
            $supplierPayment->delete();
        });

        return redirect()->route('pos.supplier-payments.index')
            ->with('success', 'Supplier payment deleted successfully and financial records restored.');
    }
}
