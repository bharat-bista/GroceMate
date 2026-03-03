<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\Income;
use App\Models\POS\Customer;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    /**
     * Display a listing of the income records.
     */
    public function index(Request $request)
    {
        $incomes = Income::with(['customer'])
            ->when($request->q, function ($query, $search) {
                $query->where('reference_no', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->orderBy('transaction_date', 'desc')
            ->paginate(2);

        return view('pos.incomes.index', compact('incomes'));
    }

    /**
     * Show the form for creating a new income record.
     */
    public function create(Request $request)
    {
        $customers = Customer::all();
        $paymentMethods = ['cash', 'bank', 'Esewa', 'Khalti'];
        
        // Pre-select customer if coming from customer detail page
        $selectedCustomerId = $request->get('customer_id');
        
        return view('pos.incomes.create', compact('customers', 'paymentMethods', 'selectedCustomerId'));
    }

    /**
     * Store a newly created income record in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference_no' => 'nullable|string|max:100',
            'customer_id' => 'nullable|exists:customers,id',
            'transaction_date' => 'required|date',
            'amount_received' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank,Esewa,Khalti',
            'income_type' => 'required|in:Sale,Due Collection,Other',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['created_by'] = auth()->id();
        
        // Check if this is a due collection for a completed invoice
        if ($validated['income_type'] === 'Due Collection' && !empty($validated['description'])) {
            // Extract invoice number from description
            if (preg_match('/Invoice #(\w+)/', $validated['description'], $matches)) {
                $invoiceNo = $matches[1];
                $invoice = \App\Models\POS\Invoice::where('invoice_no', $invoiceNo)->first();
                
                if ($invoice && $invoice->status === 'Complete') {
                    return redirect()->back()
                        ->with('error', "Invoice #{$invoiceNo} due is already cleared. This invoice is marked as Complete.")
                        ->withInput();
                }
            }
        }
        
        $income = Income::create($validated);

        // Reduce customer's total due if income type is "Due Collection"
        if ($income->income_type === 'Due Collection' && $income->customer_id) {
            $customer = Customer::find($income->customer_id);
            if ($customer && $customer->total_due > 0) {
                $newDue = max(0, $customer->total_due - $income->amount_received);
                $customer->update(['total_due' => $newDue]);
            }
            
            // Update invoice status if this is a due collection
            if (!empty($income->description)) {
                // Try multiple patterns to extract invoice number
                $patterns = [
                    '/Invoice #(\w+)/',           // Invoice #INV-2026-0007
                    '/INV-(\w+)/',                 // INV-2026-0007
                    '/(\w+-\d+-\d+)/',             // 2026-0007 format
                ];
                
                $invoiceNo = null;
                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $income->description, $matches)) {
                        $invoiceNo = $matches[1];
                        // If we got a partial match, reconstruct the full invoice number
                        if (!str_contains($matches[1], 'INV-')) {
                            $invoiceNo = 'INV-' . $matches[1];
                        }
                        break;
                    }
                }
                
                if ($invoiceNo) {
                    $invoice = \App\Models\POS\Invoice::where('invoice_no', $invoiceNo)->first();
                    if ($invoice) {
                        $invoice->updateStatus();
                    }
                }
            }
        }

        return redirect()->route('pos.income.index')->with('success', 'Income record created successfully.');
    }

    /**
     * Show the form for editing the specified income record.
     */
    public function edit(Income $income)
    {
        $customers = Customer::all();
        $paymentMethods = ['cash', 'bank', 'Esewa', 'Khalti'];
        return view('pos.incomes.edit', compact('income', 'customers', 'paymentMethods'));
    }

    /**
     * Update the specified income record in storage.
     */
    public function update(Request $request, Income $income)
    {
        $validated = $request->validate([
            'reference_no' => 'nullable|string|max:100',
            'customer_id' => 'nullable|exists:customers,id',
            'transaction_date' => 'required|date',
            'amount_received' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank,Esewa,Khalti',
            'income_type' => 'required|in:Sale,Due Collection,Other',
            'description' => 'nullable|string|max:500',
        ]);

        $oldIncomeType = $income->income_type;
        $oldAmount = $income->amount_received;
        $oldCustomerId = $income->customer_id;

        $income->update($validated);

        // Handle due amount changes
        $this->handleDueAmountChanges($income, $oldIncomeType, $oldAmount, $oldCustomerId);

        return redirect()->route('pos.income.index')->with('success', 'Income record updated successfully.');
    }

    /**
     * Handle customer due amount changes when income is updated
     */
    private function handleDueAmountChanges($income, $oldIncomeType, $oldAmount, $oldCustomerId)
    {
        $newIncomeType = $income->income_type;
        $newAmount = $income->amount_received;
        $newCustomerId = $income->customer_id;

        // If old record was Due Collection, restore the old amount to customer's due
        if ($oldIncomeType === 'Due Collection' && $oldCustomerId) {
            $oldCustomer = Customer::find($oldCustomerId);
            if ($oldCustomer) {
                $oldCustomer->increment('total_due', $oldAmount);
            }
        }

        // If new record is Due Collection, reduce the new amount from customer's due
        if ($newIncomeType === 'Due Collection' && $newCustomerId) {
            $newCustomer = Customer::find($newCustomerId);
            if ($newCustomer && $newCustomer->total_due > 0) {
                $newDue = max(0, $newCustomer->total_due - $newAmount);
                $newCustomer->update(['total_due' => $newDue]);
            }
        }
    }

    /**
     * Remove the specified income record from storage.
     */
    public function destroy(Income $income)
    {
        // Restore due amount if income type was "Due Collection"
        if ($income->income_type === 'Due Collection' && $income->customer_id) {
            $customer = Customer::find($income->customer_id);
            if ($customer) {
                $customer->increment('total_due', $income->amount_received);
            }
        }

        $income->delete();

        return redirect()->route('pos.income.index')->with('success', 'Income record deleted successfully.');
    }
}
