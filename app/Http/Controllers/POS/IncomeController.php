<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\Income;
use App\Models\POS\Customer;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    /**
     * Display a listing of the income records.
     */
    public function index(Request $request)
    {
        $incomes = Income::with(['customer', 'business'])
    ->when($request->q, function ($query, $search) {
        $query->where('reference_no', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('notes', 'like', "%{$search}%");
    })
    ->orderBy('created_at', 'desc')
    ->orderBy('transaction_date', 'desc')
    ->orderBy('id', 'desc')
    ->paginate(15);

        // ── Dashboard Statistics ──────────────────────────────────────────────

        // Total income = sum of all positive amount_received only
        $totalIncome = Income::where('amount_received', '>', 0)
                             ->sum('amount_received');

        // This month income (positive only)
        $thisMonthIncome = Income::where('amount_received', '>', 0)
                                 ->whereMonth('transaction_date', now()->month)
                                 ->whereYear('transaction_date', now()->year)
                                 ->sum('amount_received');

        // Today income (positive only)
        $todayIncome = Income::where('amount_received', '>', 0)
                             ->whereDate('transaction_date', today())
                             ->sum('amount_received');

        // ── Business Income Statistics ────────────────────────────────────────

        $businessIncomeStats = Business::withCount(['incomes' => function ($query) {
                $query->whereMonth('transaction_date', now()->month)
                      ->whereYear('transaction_date', now()->year);
            }])
            ->withSum(['incomes' => function ($query) {
                $query->whereMonth('transaction_date', now()->month)
                      ->whereYear('transaction_date', now()->year);
            }], 'amount_received')
            ->whereHas('incomes')
            ->orderBy('incomes_sum_amount_received', 'desc')
            ->get();

        // ✅ FIXED: Use businesses.balance column as single source of truth
        // Income model events (created/updated/deleted) keep this column accurate
        // Old formula was: totalIncome - totalPayments which double-counted
        // supplier payments (they are already stored as negative amount_received)
        $businessIncomeStats->each(function ($business) {
            $fresh = Business::find($business->id);
            $business->current_balance = $fresh->balance ?? 0;
        });

        // ── Recent Transactions ───────────────────────────────────────────────

        $recentIncomes = Income::with(['customer', 'business'])
                               ->orderBy('transaction_date', 'desc')
                               ->limit(5)
                               ->get();

        // ── Payment Method Statistics ─────────────────────────────────────────

        $paymentStats = Income::select(
                                'payment_method',
                                DB::raw('COUNT(*) as count'),
                                DB::raw('SUM(amount_received) as total')
                            )
                            ->whereMonth('transaction_date', now()->month)
                            ->whereYear('transaction_date', now()->year)
                            ->groupBy('payment_method')
                            ->get();

        return view('pos.incomes.index', compact(
            'incomes',
            'totalIncome',
            'thisMonthIncome',
            'todayIncome',
            'businessIncomeStats',
            'recentIncomes',
            'paymentStats'
        ));
    }

    /**
     * Show the form for creating a new income record.
     */
    public function create(Request $request)
    {
        $customers          = Customer::all();
        $businesses         = Business::all();
        $paymentMethods     = ['cash', 'bank', 'Esewa', 'Khalti'];
        $selectedCustomerId = $request->get('customer_id');

        return view('pos.incomes.create', compact(
            'customers', 'businesses', 'paymentMethods', 'selectedCustomerId'
        ));
    }

    /**
     * Store a newly created income record in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference_no'     => 'nullable|string|max:100',
            'customer_id'      => 'nullable|exists:customers,id',
            'business_id'      => 'nullable|exists:businesses,id',
            'transaction_date' => 'required|date',
            'amount_received'  => 'required|numeric|min:0',
            'payment_method'   => 'required|in:cash,bank,Esewa,Khalti',
            'income_type'      => 'required|in:Sale,Due Collection,Other',
            'description'      => 'nullable|string|max:500',
        ]);

        $validated['created_by'] = auth()->id();

        // Check if this is a due collection for a completed invoice
        if ($validated['income_type'] === 'Due Collection' && !empty($validated['description'])) {
            if (preg_match('/Invoice #(\w+)/', $validated['description'], $matches)) {
                $invoiceNo = $matches[1];
                $invoice   = \App\Models\POS\Invoice::where('invoice_no', $invoiceNo)->first();

                if ($invoice && $invoice->status === 'Complete') {
                    return redirect()->back()
                        ->with('error', "Invoice #{$invoiceNo} due is already cleared.")
                        ->withInput();
                }
            }
        }

        $income = Income::create($validated);
        // Income model created event automatically updates business balance ✅

        // Reduce customer due if Due Collection
        if ($income->income_type === 'Due Collection' && $income->customer_id) {
            $customer = Customer::find($income->customer_id);
            if ($customer && $customer->total_due > 0) {
                $newDue = max(0, $customer->total_due - $income->amount_received);
                $customer->update(['total_due' => $newDue]);
            }

            // Update invoice status
            if (!empty($income->description)) {
                $patterns = [
                    '/Invoice #(\w+)/',
                    '/INV-(\w+)/',
                    '/(\w+-\d+-\d+)/',
                ];

                $invoiceNo = null;
                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $income->description, $matches)) {
                        $invoiceNo = $matches[1];
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

        return redirect()->route('pos.income.index')
            ->with('success', '✅ Income record created successfully.');
    }

    /**
     * Show the form for editing the specified income record.
     */
    public function edit(Income $income)
    {
        $customers      = Customer::all();
        $businesses     = Business::all();
        $paymentMethods = ['cash', 'bank', 'Esewa', 'Khalti'];

        return view('pos.incomes.edit', compact(
            'income', 'customers', 'businesses', 'paymentMethods'
        ));
    }

    /**
     * Update the specified income record in storage.
     */
    public function update(Request $request, Income $income)
    {
        $validated = $request->validate([
            'reference_no'     => 'nullable|string|max:100',
            'customer_id'      => 'nullable|exists:customers,id',
            'business_id'      => 'nullable|exists:businesses,id',
            'transaction_date' => 'required|date',
            'amount_received'  => 'required|numeric|min:0',
            'payment_method'   => 'required|in:cash,bank,Esewa,Khalti',
            'income_type'      => 'required|in:Sale,Due Collection,Other',
            'description'      => 'nullable|string|max:500',
        ]);

        $oldIncomeType = $income->income_type;
        $oldAmount     = $income->amount_received;
        $oldCustomerId = $income->customer_id;

        // Income model updated event automatically adjusts business balance ✅
        $income->update($validated);

        $this->handleDueAmountChanges($income, $oldIncomeType, $oldAmount, $oldCustomerId);

        return redirect()->route('pos.income.index')
            ->with('success', '✅ Income record updated successfully.');
    }

    /**
     * Handle customer due amount changes when income is updated.
     */
    private function handleDueAmountChanges($income, $oldIncomeType, $oldAmount, $oldCustomerId)
    {
        $newIncomeType = $income->income_type;
        $newAmount     = $income->amount_received;
        $newCustomerId = $income->customer_id;

        // Restore old due if it was Due Collection
        if ($oldIncomeType === 'Due Collection' && $oldCustomerId) {
            $oldCustomer = Customer::find($oldCustomerId);
            if ($oldCustomer) {
                $oldCustomer->increment('total_due', $oldAmount);
            }
        }

        // Apply new due deduction if Due Collection
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
        // Restore customer due if it was Due Collection
        if ($income->income_type === 'Due Collection' && $income->customer_id) {
            $customer = Customer::find($income->customer_id);
            if ($customer) {
                $customer->increment('total_due', $income->amount_received);
            }
        }

        // Income model deleted event automatically restores business balance ✅
        $income->delete();

        return redirect()->route('pos.income.index')
            ->with('success', '✅ Income record deleted successfully.');
    }
}