<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\Customer;
use App\Models\Business;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers with search functionality.
     */
    public function index(Request $request)
    {
        $query = Customer::query();
        $dueExpression = $this->customerDueExpression();

        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('customer_type') && !empty($request->customer_type)) {
            $query->where('customer_type', $request->customer_type);
        }

        if ($request->has('due_status') && !empty($request->due_status)) {
            if ($request->due_status === 'with_due') {
                $query->whereRaw("({$dueExpression}) > 0");
            } elseif ($request->due_status === 'no_due') {
                $query->whereRaw("({$dueExpression}) <= 0");
            }
        }

        $customers = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $customers->getCollection()->transform(function ($customer) {
            $customer->calculated_total_due = $customer->calculateTotalDue();
            return $customer;
        });

        $grandTotalDue = (float) Customer::query()
            ->selectRaw("COALESCE(SUM({$dueExpression}), 0) as grand_total_due")
            ->value('grand_total_due');

        $customersWithDue = Customer::query()
            ->whereRaw("({$dueExpression}) > 0")
            ->count();

        return view('pos.customers.index', compact('customers', 'grandTotalDue', 'customersWithDue'));
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        return view('pos.customers.create');
    }

    /**
     * Display the specified customer with their income records.
     */
    public function show(Customer $customer)
    {
        $customer->load(['invoices', 'incomes']);
        $customer->syncTotalDue();

        if (request()->ajax() && request()->get('ledger_page')) {
            $currentPage = request()->get('ledger_page', 1);
            $allLedgerTransactions = $this->getLedgerTransactions($customer);

            $ledgerTransactions = new \Illuminate\Pagination\LengthAwarePaginator(
                $allLedgerTransactions->forPage($currentPage, 10),
                $allLedgerTransactions->count(),
                10,
                $currentPage,
                ['path' => request()->url(), 'pageName' => 'ledger_page']
            );

            return response()->json([
                'pagination' => $ledgerTransactions->links('pagination::tailwind')->toHtml()
            ]);
        }

        $incomes = \App\Models\POS\Income::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'incomes_page');

        $sales = \App\Models\POS\Invoice::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'sales_page');

        $chartData = $this->prepareChartData($customer);
        $allLedgerTransactions = $this->getLedgerTransactions($customer);

        $currentPage = request()->get('ledger_page', 1);
        $ledgerTransactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $allLedgerTransactions->forPage($currentPage, 20),
            $allLedgerTransactions->count(),
            20,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'ledger_page']
        );

        return view('pos.customers.show', compact('customer', 'incomes', 'sales', 'chartData', 'ledgerTransactions'));
    }

    /**
     * Update status for all customer invoices.
     */
    private function updateAllInvoiceStatuses($customer)
    {
        $invoices = \App\Models\POS\Invoice::where('customer_id', $customer->id)->get();

        foreach ($invoices as $invoice) {
            $invoice->updateStatus();
            $invoice->save();
        }
    }

    private function customerDueExpression(): string
    {
        return "COALESCE(customers.opening_due, 0)
            + COALESCE((
                SELECT SUM(invoices.total_cost)
                FROM invoices
                WHERE invoices.customer_id = customers.id
                  AND invoices.payment_method = 'credit'
            ), 0)
            - COALESCE((
                SELECT SUM(incomes.amount_received)
                FROM incomes
                WHERE incomes.customer_id = customers.id
                  AND incomes.amount_received > 0
            ), 0)";
    }

    /**
     * Get all ledger transactions for customer.
     */
    private function getLedgerTransactions($customer)
    {
        $transactions = collect();

        if (($customer->opening_due ?? 0) > 0) {
            $openingDateTime = optional($customer->created_at)->copy() ?? now();

            $transactions->push([
                'id' => 'opening',
                'date' => $openingDateTime->format('Y-m-d'),
                'datetime' => $openingDateTime->subSecond()->format('Y-m-d H:i:s'),
                'reference' => 'OPENING',
                'description' => 'Opening Balance',
                'debit' => 0,
                'credit' => $customer->opening_due,
                'type' => 'opening',
                'balance' => 0,
            ]);
        }

        $sales = \App\Models\POS\Invoice::where('customer_id', $customer->id)
            ->where('payment_method', 'credit')
            ->get()
            ->map(function ($sale) {
                $saleDateTime = $sale->invoice_date
                    ? $sale->invoice_date->copy()->setTimeFrom($sale->created_at)
                    : $sale->created_at->copy();

                return [
                    'id' => $sale->id,
                    'date' => $saleDateTime->format('Y-m-d'),
                    'datetime' => $saleDateTime->format('Y-m-d H:i:s'),
                    'reference' => $sale->invoice_no,
                    'description' => 'Credit Sale - Invoice #' . $sale->invoice_no,
                    'debit' => 0,
                    'credit' => $sale->total_cost,
                    'type' => 'sale',
                    'balance' => 0,
                ];
            });

        $incomes = \App\Models\POS\Income::where('customer_id', $customer->id)->get()
            ->map(function ($income) {
                $incomeDateTime = \Carbon\Carbon::parse($income->transaction_date)->setTimeFrom($income->created_at);

                return [
                    'id' => $income->id,
                    'date' => $income->transaction_date,
                    'datetime' => $incomeDateTime->format('Y-m-d H:i:s'),
                    'reference' => $income->reference_no ?? 'INC-' . str_pad($income->id, 4, '0', STR_PAD_LEFT),
                    'description' => 'Payment Received',
                    'debit' => $income->amount_received,
                    'credit' => 0,
                    'type' => 'income',
                    'balance' => 0,
                ];
            });

        $allTransactions = $transactions
            ->merge($sales)
            ->merge($incomes)
            ->sortBy('datetime')
            ->values();

        $runningBalance = 0;
        $allTransactions = $allTransactions->map(function ($txn) use (&$runningBalance) {
            $runningBalance = $runningBalance + $txn['credit'] - $txn['debit'];
            $txn['balance'] = $runningBalance;
            return $txn;
        });

        return $allTransactions->sortByDesc('datetime')->values();
    }

    /**
     * Prepare chart data for customer income and sales for all time periods.
     */
    private function prepareChartData($customer)
    {
        return [
            'daily' => $this->getDailyData($customer),
            'weekly' => $this->getWeeklyData($customer),
            'monthly' => $this->getMonthlyData($customer),
            'yearly' => $this->getYearlyData($customer),
        ];
    }

    /**
     * Get daily data for last 30 days.
     */
    private function getDailyData($customer)
    {
        $days = [];
        $incomeData = [];
        $salesData = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('M d');

            $incomeTotal = \App\Models\POS\Income::where('customer_id', $customer->id)
                ->whereDate('transaction_date', $date)
                ->sum('amount_received');
            $incomeData[] = $incomeTotal;

            $salesTotal = \App\Models\POS\Invoice::where('customer_id', $customer->id)
                ->whereDate('created_at', $date)
                ->sum('total_cost');
            $salesData[] = $salesTotal;
        }

        return [
            'labels' => $days,
            'income' => $incomeData,
            'sales' => $salesData,
        ];
    }

    /**
     * Get weekly data for last 12 weeks.
     */
    private function getWeeklyData($customer)
    {
        $weeks = [];
        $incomeData = [];
        $salesData = [];

        for ($i = 11; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();

            $weeks[] = 'Week ' . $weekStart->format('m/d') . '-' . $weekEnd->format('m/d');

            $incomeTotal = \App\Models\POS\Income::where('customer_id', $customer->id)
                ->whereBetween('transaction_date', [$weekStart, $weekEnd])
                ->sum('amount_received');
            $incomeData[] = $incomeTotal;

            $salesTotal = \App\Models\POS\Invoice::where('customer_id', $customer->id)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->sum('total_cost');
            $salesData[] = $salesTotal;
        }

        return [
            'labels' => $weeks,
            'income' => $incomeData,
            'sales' => $salesData,
        ];
    }

    /**
     * Get monthly data for last 6 months.
     */
    private function getMonthlyData($customer)
    {
        $months = [];
        $incomeData = [];
        $salesData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $months[] = $month->format('M Y');

            $incomeTotal = \App\Models\POS\Income::where('customer_id', $customer->id)
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount_received');
            $incomeData[] = $incomeTotal;

            $salesTotal = \App\Models\POS\Invoice::where('customer_id', $customer->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_cost');
            $salesData[] = $salesTotal;
        }

        return [
            'labels' => $months,
            'income' => $incomeData,
            'sales' => $salesData,
        ];
    }

    /**
     * Get yearly data for last 5 years.
     */
    private function getYearlyData($customer)
    {
        $years = [];
        $incomeData = [];
        $salesData = [];

        for ($i = 4; $i >= 0; $i--) {
            $year = now()->subYears($i);
            $yearStart = $year->copy()->startOfYear();
            $yearEnd = $year->copy()->endOfYear();

            $years[] = $year->format('Y');

            $incomeTotal = \App\Models\POS\Income::where('customer_id', $customer->id)
                ->whereBetween('transaction_date', [$yearStart, $yearEnd])
                ->sum('amount_received');
            $incomeData[] = $incomeTotal;

            $salesTotal = \App\Models\POS\Invoice::where('customer_id', $customer->id)
                ->whereBetween('created_at', [$yearStart, $yearEnd])
                ->sum('total_cost');
            $salesData[] = $salesTotal;
        }

        return [
            'labels' => $years,
            'income' => $incomeData,
            'sales' => $salesData,
        ];
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
            'pan_number' => 'nullable|string|max:20',
            'customer_type' => 'required|in:retail,wholesale,regular',
            'opening_due' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['opening_due'] = (int) round($validated['opening_due'] ?? 0);
        $validated['total_due'] = $validated['opening_due'];

        $defaultBusinessId = Business::min('id');
        if ($defaultBusinessId) {
            $validated['business_id'] = $defaultBusinessId;
        }

        Customer::create($validated);

        return redirect()->route('pos.customers.index')
            ->with('success', 'Customer added successfully.');
    }

    /**
     * Show the form for editing a customer.
     */
    public function edit(Customer $customer)
    {
        $customer->syncTotalDue();

        return view('pos.customers.edit', compact('customer'));
    }

    /**
     * Update a customer.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'vat_number' => 'nullable|string|max:50',
            'pan_number' => 'nullable|string|max:20',
            'customer_type' => 'required|in:retail,wholesale,regular',
            'opening_due' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if (isset($validated['opening_due'])) {
            $customer->opening_due = $validated['opening_due'];
        }

        $customer->update($validated);
        $customer->syncTotalDue();

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
