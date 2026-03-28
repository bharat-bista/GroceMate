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

    /**
     * Display the specified supplier with their purchase and payment records.
     */
    public function show(Supplier $supplier)
    {
        $supplier->load(['purchases', 'supplierPayments']);
        $supplier->syncTotalDue();

        // Get supplier's purchase records with pagination
        $purchases = \App\Models\Purchase::where('supplier_id', $supplier->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'purchases_page');

        // Get supplier's payment records with pagination
        $payments = \App\Models\SupplierPayment::where('supplier_id', $supplier->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5, ['*'], 'payments_page');

        // Prepare chart data for all time periods
        $chartData = $this->prepareChartData($supplier);

        // Get ledger transactions (purchases + payments)
        $allLedgerTransactions = $this->getLedgerTransactions($supplier);
        
        // Paginate ledger transactions (20 per page)
        $currentPage = request()->get('ledger_page', 1);
        $ledgerTransactions = new \Illuminate\Pagination\LengthAwarePaginator(
            $allLedgerTransactions->forPage($currentPage, 20),
            $allLedgerTransactions->count(),
            20,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'ledger_page']
        );

        return view('inventory.suppliers.show', compact('supplier', 'purchases', 'payments', 'chartData', 'ledgerTransactions'));
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

        $supplier->update($data);
        $supplier->syncTotalDue();

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

    /**
     * Get all ledger transactions for supplier
     */
    private function getLedgerTransactions($supplier)
    {
        $transactions = collect();

        // Opening Balance Entry
        if (($supplier->opening_due ?? 0) > 0) {
            $openingDateTime = optional($supplier->created_at)->copy() ?? now();

            $transactions->push([
                'id' => 'opening',
                'date' => $openingDateTime->format('Y-m-d'),
                'datetime' => $openingDateTime->subSecond()->format('Y-m-d H:i:s'),
                'reference' => 'OPENING',
                'description' => 'Opening Balance',
                'debit' => 0,
                'credit' => $supplier->opening_due,
                'type' => 'opening',
                'balance' => 0
            ]);
        }

        $purchases = \App\Models\Purchase::where('supplier_id', $supplier->id)->get()
            ->map(function ($purchase) {
                $purchaseDateTime = $purchase->purchase_date
                    ? $purchase->purchase_date->copy()->setTimeFrom($purchase->created_at)
                    : $purchase->created_at->copy();

                return [
                    'id' => $purchase->id,
                    'date' => $purchaseDateTime->format('Y-m-d'),
                    'datetime' => $purchaseDateTime->format('Y-m-d H:i:s'),
                    'reference' => $purchase->invoice_no ?? 'PUR-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT),
                    'description' => 'Purchase - ' . ($purchase->invoice_no ?? 'Invoice #' . $purchase->id),
                    'debit' => 0,
                    'credit' => $purchase->total_cost,
                    'type' => 'purchase',
                    'balance' => 0
                ];
            });

        $payments = \App\Models\SupplierPayment::where('supplier_id', $supplier->id)->get()
            ->map(function ($payment) {
                $paymentDateTime = \Carbon\Carbon::parse($payment->date)->setTimeFrom($payment->created_at);

                return [
                    'id' => $payment->id,
                    'date' => $payment->date,
                    'datetime' => $paymentDateTime->format('Y-m-d H:i:s'),
                    'reference' => $payment->payment_reference ?? 'PAY-' . str_pad($payment->id, 4, '0', STR_PAD_LEFT),
                    'description' => 'Payment Made',
                    'debit' => $payment->amount,
                    'credit' => 0,
                    'type' => 'payment',
                    'balance' => 0
                ];
            });

        $allTransactions = $transactions
            ->merge($purchases)
            ->merge($payments)
            ->sortBy('datetime')
            ->values();

        // Calculate running balance
        $runningBalance = 0;

        $allTransactions = $allTransactions->map(function ($txn) use (&$runningBalance) {
            $runningBalance = $runningBalance + $txn['credit'] - $txn['debit'];
            $txn['balance'] = $runningBalance;
            return $txn;
        });

        return $allTransactions->sortByDesc('datetime')->values();
    }

    /**
     * Prepare chart data for supplier purchases and payments for all time periods
     */
    private function prepareChartData($supplier)
    {
        return [
            'daily' => $this->getDailyData($supplier),
            'weekly' => $this->getWeeklyData($supplier),
            'monthly' => $this->getMonthlyData($supplier),
            'yearly' => $this->getYearlyData($supplier)
        ];
    }

    /**
     * Get daily data for last 30 days
     */
    private function getDailyData($supplier)
    {
        $days = [];
        $paymentData = [];
        $purchaseData = [];

        // Get last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('M d');
            
            // Get payment data for this day
            $paymentTotal = \App\Models\SupplierPayment::where('supplier_id', $supplier->id)
                ->whereDate('date', $date)
                ->sum('amount');
            $paymentData[] = $paymentTotal;
            
            // Get purchase data for this day
            $purchaseTotal = \App\Models\Purchase::where('supplier_id', $supplier->id)
                ->whereDate('created_at', $date)
                ->sum('total_cost');
            $purchaseData[] = $purchaseTotal;
        }

        return [
            'labels' => $days,
            'payments' => $paymentData,
            'purchases' => $purchaseData
        ];
    }

    /**
     * Get weekly data for last 12 weeks
     */
    private function getWeeklyData($supplier)
    {
        $weeks = [];
        $paymentData = [];
        $purchaseData = [];

        // Get last 12 weeks
        for ($i = 11; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            
            $weeks[] = "Week " . $weekStart->format('m/d') . "-" . $weekEnd->format('m/d');
            
            // Get payment data for this week
            $paymentTotal = \App\Models\SupplierPayment::where('supplier_id', $supplier->id)
                ->whereBetween('date', [$weekStart, $weekEnd])
                ->sum('amount');
            $paymentData[] = $paymentTotal;
            
            // Get purchase data for this week
            $purchaseTotal = \App\Models\Purchase::where('supplier_id', $supplier->id)
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->sum('total_cost');
            $purchaseData[] = $purchaseTotal;
        }

        return [
            'labels' => $weeks,
            'payments' => $paymentData,
            'purchases' => $purchaseData
        ];
    }

    /**
     * Get monthly data for last 6 months
     */
    private function getMonthlyData($supplier)
    {
        $months = [];
        $paymentData = [];
        $purchaseData = [];

        // Get last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $months[] = $month->format('M Y');
            
            // Get payment data for this month
            $paymentTotal = \App\Models\SupplierPayment::where('supplier_id', $supplier->id)
                ->whereBetween('date', [$monthStart, $monthEnd])
                ->sum('amount');
            $paymentData[] = $paymentTotal;
            
            // Get purchase data for this month
            $purchaseTotal = \App\Models\Purchase::where('supplier_id', $supplier->id)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_cost');
            $purchaseData[] = $purchaseTotal;
        }

        return [
            'labels' => $months,
            'payments' => $paymentData,
            'purchases' => $purchaseData
        ];
    }

    /**
     * Get yearly data for last 5 years
     */
    private function getYearlyData($supplier)
    {
        $years = [];
        $paymentData = [];
        $purchaseData = [];

        // Get last 5 years
        for ($i = 4; $i >= 0; $i--) {
            $year = now()->subYears($i);
            $yearStart = $year->copy()->startOfYear();
            $yearEnd = $year->copy()->endOfYear();
            
            $years[] = $year->format('Y');
            
            // Get payment data for this year
            $paymentTotal = \App\Models\SupplierPayment::where('supplier_id', $supplier->id)
                ->whereBetween('date', [$yearStart, $yearEnd])
                ->sum('amount');
            $paymentData[] = $paymentTotal;
            
            // Get purchase data for this year
            $purchaseTotal = \App\Models\Purchase::where('supplier_id', $supplier->id)
                ->whereBetween('created_at', [$yearStart, $yearEnd])
                ->sum('total_cost');
            $purchaseData[] = $purchaseTotal;
        }

        return [
            'labels' => $years,
            'payments' => $paymentData,
            'purchases' => $purchaseData
        ];
    }
}
