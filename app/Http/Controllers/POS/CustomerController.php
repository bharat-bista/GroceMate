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
        // Build query with filters
        $query = Customer::query();

        // Search filter
        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Customer type filter
        if ($request->has('customer_type') && !empty($request->customer_type)) {
            $query->where('customer_type', $request->customer_type);
        }

        // Due status filter
        if ($request->has('due_status') && !empty($request->due_status)) {
            if ($request->due_status === 'with_due') {
                $query->whereHas('invoices', function ($invoiceQuery) {
                    $invoiceQuery->havingRaw('SUM(total_cost) > COALESCE(SUM(paid_amount), 0)');
                });
            } elseif ($request->due_status === 'no_due') {
                $query->whereDoesntHave('invoices')
                    ->orWhereHas('invoices', function ($invoiceQuery) {
                        $invoiceQuery->havingRaw('SUM(total_cost) <= COALESCE(SUM(paid_amount), 0)');
                    });
            }
        }

        $customers = $query->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Calculate correct total due for each customer
        $customers->getCollection()->transform(function ($customer) {
            $totalInvoices = \App\Models\POS\Invoice::where('customer_id', $customer->id)->sum('total_cost');
            $totalPayments = \App\Models\POS\Income::where('customer_id', $customer->id)->sum('amount_received');
            $customer->calculated_total_due = ($customer->opening_due ?? 0) + $totalInvoices - $totalPayments;
            return $customer;
        });

        // Get total due from all customers (using the total_due field directly)
        $grandTotalDue = Customer::sum('total_due');
        
        // Get customers with due count
        $customersWithDue = Customer::where('total_due', '>', 0)->count();

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
        // Handle AJAX request for ledger pagination
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

        // Get customer's income records with pagination (sorted by date and time)
        $incomes = \App\Models\POS\Income::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'incomes_page');

        // Get customer's sales records with pagination (sorted by date and time)
        $sales = \App\Models\POS\Invoice::where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'sales_page');

        // Prepare chart data for all time periods
        $chartData = $this->prepareChartData($customer);

        // Get ledger transactions (sales + incomes) - show all records with pagination
        $allLedgerTransactions = $this->getLedgerTransactions($customer);
        
        // Paginate ledger transactions (20 per page) - fix the pagination
        $currentPage = request()->get('ledger_page', 1);
        $ledgerTransactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $allLedgerTransactions->forPage($currentPage, 20),
            $allLedgerTransactions->count(),
            20,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'ledger_page']
        );

        // Auto-update invoice statuses for all customer invoices
        // Temporarily disabled - causing total_due to reset to 0
        // $this->updateAllInvoiceStatuses($customer);

        return view('pos.customers.show', compact('customer', 'incomes', 'sales', 'chartData', 'ledgerTransactions'));
    }

    /**
     * Update status for all customer invoices
     */
    private function updateAllInvoiceStatuses($customer)
    {
        $invoices = \App\Models\POS\Invoice::where('customer_id', $customer->id)->get();
        
        foreach ($invoices as $invoice) {
            // Always check individual invoice status based on actual payments
            // Don't use customer's total_due as it changes with new invoices
            $invoice->updateStatus();
            $invoice->save();
        }
    }

    /**
     * Get all ledger transactions for customer
     */
    private function getLedgerTransactions($customer)
{
    $transactions = collect();

    // ✅ Opening Balance Entry
    if (($customer->opening_due ?? 0) > 0) {
        $transactions->push([
            'id' => 'opening',
            'date' => now()->format('Y-m-d'),
            'datetime' => now()->format('Y-m-d H:i:s'),
            'reference' => 'OPENING',
            'description' => 'Opening Balance',
            'debit' => $customer->opening_due,
            'credit' => 0,
            'type' => 'opening',
            'balance' => 0
        ]);
    }

    $sales = \App\Models\POS\Invoice::where('customer_id', $customer->id)->get()
        ->map(function ($sale) {
            return [
                'id' => $sale->id,
                'date' => $sale->created_at->format('Y-m-d'),
                'datetime' => $sale->created_at->format('Y-m-d H:i:s'),
                'reference' => $sale->invoice_no,
                'description' => 'Sale - Invoice #' . $sale->invoice_no,
                'debit' => $sale->total_cost,
                'credit' => 0,
                'type' => 'sale',
                'balance' => 0
            ];
        });

    $incomes = \App\Models\POS\Income::where('customer_id', $customer->id)->get()
        ->map(function ($income) {
            return [
                'id' => $income->id,
                'date' => $income->transaction_date,
                'datetime' => $income->created_at->format('Y-m-d H:i:s'),
                'reference' => $income->reference_no ?? 'INC-' . str_pad($income->id, 4, '0', STR_PAD_LEFT),
                'description' => 'Payment Received',
                'debit' => 0,
                'credit' => $income->amount_received,
                'type' => 'income',
                'balance' => 0
            ];
        });

    $allTransactions = $transactions
        ->merge($sales)
        ->merge($incomes)
        ->sortBy('datetime')
        ->values();

    // ✅ Correct Calculation
    $runningBalance = 0;

    $allTransactions = $allTransactions->map(function ($txn) use (&$runningBalance) {
        $runningBalance = $runningBalance + $txn['debit'] - $txn['credit'];
        $txn['balance'] = $runningBalance;
        return $txn;
    });

    return $allTransactions->sortByDesc('datetime')->values();
}

    /**
     * Prepare chart data for customer income and sales for all time periods
     */
    private function prepareChartData($customer)
    {
        return [
            'daily' => $this->getDailyData($customer),
            'weekly' => $this->getWeeklyData($customer),
            'monthly' => $this->getMonthlyData($customer),
            'yearly' => $this->getYearlyData($customer)
        ];
    }

    /**
     * Get daily data for last 30 days
     */
    private function getDailyData($customer)
    {
        $days = [];
        $incomeData = [];
        $salesData = [];

        // Get last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('M d');
            
            // Get income data for this day
            $incomeTotal = \App\Models\POS\Income::where('customer_id', $customer->id)
                ->whereDate('transaction_date', $date)
                ->sum('amount_received');
            $incomeData[] = $incomeTotal;
            
            // Get sales data for this day
            $salesTotal = \App\Models\POS\Invoice::where('customer_id', $customer->id)
                ->whereDate('created_at', $date)
                ->sum('total_cost');
            $salesData[] = $salesTotal;
        }

        return [
            'labels' => $days,
            'income' => $incomeData,
            'sales' => $salesData
        ];
    }

    /**
     * Get weekly data for last 12 weeks
     */
    private function getWeeklyData($customer)
    {
        $weeks = [];
        $incomeData = [];
        $salesData = [];

        // Get last 12 weeks
        for ($i = 11; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            
            $weeks[] = "Week " . $weekStart->format('m/d') . "-" . $weekEnd->format('m/d');
            
            // Get income data for this week
            $incomeTotal = \App\Models\POS\Income::where('customer_id', $customer->id)
                ->whereBetween('transaction_date', [$weekStart, $weekEnd])
                ->sum('amount_received');
            $incomeData[] = $incomeTotal;
            
            // Get sales data for this week
            $salesTotal = \App\Models\POS\Invoice::where('customer_id', $customer->id)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->sum('total_cost');
            $salesData[] = $salesTotal;
        }

        return [
            'labels' => $weeks,
            'income' => $incomeData,
            'sales' => $salesData
        ];
    }

    /**
     * Get monthly data for last 6 months
     */
    private function getMonthlyData($customer)
    {
        $months = [];
        $incomeData = [];
        $salesData = [];

        // Get last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $months[] = $month->format('M Y');
            
            // Get income data for this month
            $incomeTotal = \App\Models\POS\Income::where('customer_id', $customer->id)
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount_received');
            $incomeData[] = $incomeTotal;
            
            // Get sales data for this month
            $salesTotal = \App\Models\POS\Invoice::where('customer_id', $customer->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_cost');
            $salesData[] = $salesTotal;
        }

        return [
            'labels' => $months,
            'income' => $incomeData,
            'sales' => $salesData
        ];
    }

    /**
     * Get yearly data for last 5 years
     */
    private function getYearlyData($customer)
    {
        $years = [];
        $incomeData = [];
        $salesData = [];

        // Get last 5 years
        for ($i = 4; $i >= 0; $i--) {
            $year = now()->subYears($i);
            $yearStart = $year->copy()->startOfYear();
            $yearEnd = $year->copy()->endOfYear();
            
            $years[] = $year->format('Y');
            
            // Get income data for this year
            $incomeTotal = \App\Models\POS\Income::where('customer_id', $customer->id)
                ->whereBetween('transaction_date', [$yearStart, $yearEnd])
                ->sum('amount_received');
            $incomeData[] = $incomeTotal;
            
            // Get sales data for this year
            $salesTotal = \App\Models\POS\Invoice::where('customer_id', $customer->id)
                ->whereBetween('created_at', [$yearStart, $yearEnd])
                ->sum('total_cost');
            $salesData[] = $salesTotal;
        }

        return [
            'labels' => $years,
            'income' => $incomeData,
            'sales' => $salesData
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
            'pan_number' => 'nullable|string|max:20',
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