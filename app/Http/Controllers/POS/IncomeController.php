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
        // ── All Income records (both positive and negative) ──────────────────────────────────
        $query = Income::with(['customer', 'business']);

        // Search functionality
        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Date filtering
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        // Income type filtering
        if ($request->has('income_type') && !empty($request->income_type)) {
            $query->where('income_type', $request->income_type);
        }

        $incomes = $query->orderBy('created_at', 'desc')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

    // ── Dashboard Statistics (all transactions) ──────────────────────────────
    $totalIncome = Income::sum('amount_received');

    $thisMonthIncome = Income::where('amount_received', '>', 0)
        ->whereMonth('transaction_date', now()->month)
        ->whereYear('transaction_date', now()->year)
        ->sum('amount_received');

    $todayIncome = Income::where('amount_received', '>', 0)
        ->whereDate('transaction_date', today())
        ->sum('amount_received');

    // ── Business Stats ───────────────────────────────────────────────────
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

    $businessIncomeStats->each(function ($business) {
        $fresh = Business::find($business->id);
        $business->current_balance = $fresh->balance ?? 0;
    });

    // ── Payment Stats ────────────────────────────────────────────────────
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

        if ($income->customer_id) {
            $customer = Customer::find($income->customer_id);
            if ($customer) {
                $customer->syncTotalDue();
            }
        }

        // Handle Due Collection specific logic (for invoice status updates)
        if ($income->income_type === 'Due Collection') {

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
        $newCustomerId = $income->customer_id;

        foreach (collect([$oldCustomerId, $newCustomerId])->filter()->unique() as $customerId) {
            $customer = Customer::find($customerId);
            if ($customer) {
                $customer->syncTotalDue();
            }
        }
    }

    /**
     * Remove the specified income record from storage.
     */
    public function destroy(Income $income)
    {
        $customerId = $income->customer_id;

        // Income model deleted event automatically restores business balance ✅
        $income->delete();

        if ($customerId) {
            $customer = Customer::find($customerId);
            if ($customer) {
                $customer->syncTotalDue();
            }
        }

        return redirect()->route('pos.income.index')
            ->with('success', '✅ Income record deleted successfully.');
    }

    // ─────────────────────────────────────────────────────────────────
    // EXPORT METHODS
    // ─────────────────────────────────────────────────────────────────

    /**
     * Export income records to PDF, Excel, or CSV
     */
    public function export($type, Request $request)
    {
        // Get date filters (using 'from' and 'to' like purchase export)
        $from = $request->get('from');
        $to = $request->get('to');

        // Build query with same filters as index
        $query = Income::with(['customer', 'business']);

        // Apply search filter
        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('reference_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Apply date filters (using from/to like purchase export)
        if ($from && $to) {
            try {
                $fromDate = \Carbon\Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
                $toDate = \Carbon\Carbon::createFromFormat('Y-m-d', $to)->endOfDay();
                $query->whereBetween('transaction_date', [$fromDate, $toDate]);
            } catch (\Exception $e) {
                // Invalid date format, skip date filtering
            }
        } elseif ($from) {
            try {
                $fromDate = \Carbon\Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
                $query->whereDate('transaction_date', '>=', $fromDate);
            } catch (\Exception $e) {
                // Invalid date format, skip date filtering
            }
        } elseif ($to) {
            try {
                $toDate = \Carbon\Carbon::createFromFormat('Y-m-d', $to)->endOfDay();
                $query->whereDate('transaction_date', '<=', $toDate);
            } catch (\Exception $e) {
                // Invalid date format, skip date filtering
            }
        }

        // Apply income type filter
        if ($request->has('income_type') && !empty($request->income_type)) {
            $query->where('income_type', $request->income_type);
        }

        // Get filtered results
        $incomes = $query->orderBy('created_at', 'desc')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        if ($type === 'pdf') {
            $filename = 'income_report_' . now()->format('Y-m-d') . '.pdf';
            
            $html = view('pos.incomes.export-pdf', [
                'incomes' => $incomes,
                'from' => $from,
                'to' => $to
            ])->render();
            
            // Use simple PDF generation with DOMPDF style (same as purchase export)
            $pdf = new \Dompdf\Dompdf();
            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'landscape');
            $pdf->render();
            
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);
        }

        if ($type === 'excel') {
            $filename = 'income_report_' . now()->format('Y-m-d') . '.xlsx';
            
            // Generate HTML table that Excel can open (same as purchase export)
            $html = view('pos.incomes.export-excel', [
                'incomes' => $incomes,
                'from' => $from,
                'to' => $to
            ])->render();
            
            return response($html, 200, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);
        }

        if ($type === 'csv') {
            $filename = 'income_report_' . now()->format('Y-m-d') . '.csv';
            
            // Generate CSV content
            $csv = "Date,Reference,Customer,Business,Payment Method,Type,Amount,Notes\n";
            
            foreach ($incomes as $income) {
                $csv .= '"' . \Carbon\Carbon::parse($income->transaction_date ?? $income->created_at)->format('Y-m-d') . '",';
                $csv .= '"' . ($income->reference_no ?? 'INC-' . str_pad($income->id, 4, '0', STR_PAD_LEFT)) . '",';
                $csv .= '"' . ($income->customer->name ?? 'N/A') . '",';
                $csv .= '"' . ($income->business->business_name ?? 'N/A') . '",';
                $csv .= '"' . ucfirst($income->payment_method) . '",';
                $csv .= '"' . ($income->income_type ?? 'Other') . '",';
                $csv .= $income->amount_received . ',';
                $csv .= '"' . ($income->notes ?? $income->description ?? '-') . '"' . "\n";
            }
            
            return response($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);
        }

        return redirect()->back()->with('error', 'Invalid export type');
    }
}
