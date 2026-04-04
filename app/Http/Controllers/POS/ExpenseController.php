<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\POS\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['business', 'creator']);

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                    ->orWhere('expense_type', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('business', function ($bq) use ($search) {
                        $bq->where('business_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('expense_type')) {
            $query->where('expense_type', $request->expense_type);
        }

        $expenses = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $totalExpense = Expense::sum('amount');
        $thisMonthExpense = Expense::whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');
        $todayExpense = Expense::whereDate('transaction_date', today())->sum('amount');

        $expenseTypes = Expense::select('expense_type')
            ->distinct()
            ->orderBy('expense_type')
            ->pluck('expense_type');

        return view('pos.expenses.index', compact(
            'expenses',
            'totalExpense',
            'thisMonthExpense',
            'todayExpense',
            'expenseTypes'
        ));
    }

    public function create()
    {
        $businesses = Business::orderBy('business_name')->get();
        $paymentMethods = ['cash', 'bank', 'Esewa', 'Khalti'];

        return view('pos.expenses.create', compact('businesses', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reference_no' => 'nullable|string|max:100',
            'business_id' => 'nullable|exists:businesses,id',
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank,Esewa,Khalti',
            'expense_type' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        $validated['created_by'] = auth()->id();

        Expense::create($validated);

        return redirect()->route('pos.expenses.index')
            ->with('success', 'Expense record created successfully.');
    }

    public function show(Expense $expense)
    {
        $expense->load(['business', 'creator']);

        return view('pos.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $businesses = Business::orderBy('business_name')->get();
        $paymentMethods = ['cash', 'bank', 'Esewa', 'Khalti'];

        return view('pos.expenses.edit', compact('expense', 'businesses', 'paymentMethods'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validated = $request->validate([
            'reference_no' => 'nullable|string|max:100',
            'business_id' => 'nullable|exists:businesses,id',
            'transaction_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank,Esewa,Khalti',
            'expense_type' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        $expense->update($validated);

        return redirect()->route('pos.expenses.show', $expense)
            ->with('success', 'Expense record updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route('pos.expenses.index')
            ->with('success', 'Expense record deleted successfully.');
    }
}
