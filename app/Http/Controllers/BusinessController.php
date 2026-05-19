<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Purchase;
use App\Models\SupplierPayment;
use App\Models\POS\Expense;
use App\Models\POS\Income;
use App\Models\POS\Invoice;
use App\Services\EcommerceIncomeSyncService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BusinessController extends Controller
{
    public function __construct(private EcommerceIncomeSyncService $ecommerceIncomeSyncService)
    {
    }

    public function index()
    {
        $businesses = Business::latest()->get();
        return view('pos.business.index', compact('businesses'));
    }

    public function show(Business $business)
    {
        $this->ecommerceIncomeSyncService->syncBusinessOrders($business->id);

        $from = request()->get('from');
        $to = request()->get('to');
        $reportData = $this->getBusinessReportData($business, $from, $to);
        $ecommerceData = $this->getEcommerceOverviewData($business, $from, $to);

        return view('pos.business.show', array_merge($reportData, $ecommerceData, [
            'business' => $business,
            'chartData' => $this->buildChartData($business),
        ]));
    }

    public function create()
    {
        return view('pos.business.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'business_name' => $request->business_name,
            'business_type' => $request->business_type,
            'vat_no' => $request->vat_no,
            'pan_no' => $request->pan_no,
            'phone' => $request->phone,
            'address' => $request->address,
            'owner_name' => $request->owner_name,
        ];

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/img/business'), $imageName);
            $data['profile_image'] = $imageName;
        }

        Business::create($data);

        return redirect()->route('business.index')->with('success', 'Business Created Successfully!');
    }

    public function edit(Business $business)
    {
        return view('pos.business.edit', compact('business'));
    }

    public function update(Request $request, Business $business)
    {
        $request->validate([
            'business_name' => 'required|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = [
            'business_name' => $request->business_name,
            'business_type' => $request->business_type,
            'vat_no' => $request->vat_no,
            'pan_no' => $request->pan_no,
            'phone' => $request->phone,
            'address' => $request->address,
            'owner_name' => $request->owner_name,
        ];

        if ($request->hasFile('profile_image')) {
            $image = $request->file('profile_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('assets/img/business'), $imageName);
            $data['profile_image'] = $imageName;
        }

        $business->update($data);

        return redirect()->route('business.index')->with('success', 'Business Updated Successfully!');
    }

    public function destroy(Business $business)
    {
        if ($business->profile_image) {
            $imagePath = public_path('assets/img/business/' . $business->profile_image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        $business->delete();

        return redirect()->route('business.index')->with('success', 'Business Deleted Successfully!');
    }

    public function export(Business $business, $type, Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');
        $reportData = $this->getBusinessReportData($business, $from, $to);

        if ($type === 'pdf') {
            $filename = 'business_' . $business->id . '_report_' . now()->format('Y-m-d') . '.pdf';

            $html = view('pos.business.export-pdf', array_merge($reportData, [
                'business' => $business,
                'from' => $from,
                'to' => $to,
            ]))->render();

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
            $filename = 'business_' . $business->id . '_report_' . now()->format('Y-m-d') . '.xlsx';

            $html = view('pos.business.export-excel', array_merge($reportData, [
                'business' => $business,
                'from' => $from,
                'to' => $to,
            ]))->render();

            return response($html, 200, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);
        }

        if ($type === 'csv') {
            $filename = 'business_' . $business->id . '_report_' . now()->format('Y-m-d') . '.csv';

            $csv = "Business Report\n";
            $csv .= "Business Name,\"{$business->business_name}\"\n";
            $csv .= "Business Type,\"" . ($business->business_type ?? 'N/A') . "\"\n";
            $csv .= "From,\"" . ($from ?: 'All Time') . "\"\n";
            $csv .= "To,\"" . ($to ?: 'All Time') . "\"\n";
            $csv .= "\n";
            $csv .= "Summary\n";
            $csv .= "Metric,Amount\n";
            $csv .= "\"Total Purchases\",\"" . number_format($reportData['purchaseTotal'], 2, '.', '') . "\"\n";
            $csv .= "\"Total Sales\",\"" . number_format($reportData['salesTotal'], 2, '.', '') . "\"\n";
            $csv .= "\"Income Received\",\"" . number_format($reportData['incomeTotal'], 2, '.', '') . "\"\n";
            $csv .= "\"Other Income\",\"" . number_format($reportData['otherIncomeTotal'], 2, '.', '') . "\"\n";
            $csv .= "\"Sale Income Entries\",\"" . number_format($reportData['saleIncomeTotal'], 2, '.', '') . "\"\n";
            $csv .= "\"Due Collections\",\"" . number_format($reportData['dueCollectionTotal'], 2, '.', '') . "\"\n";
            $csv .= "\"Supplier Payments\",\"" . number_format($reportData['supplierPaymentTotal'], 2, '.', '') . "\"\n";
            $csv .= "\"Net Cash Flow\",\"" . number_format($reportData['netCashFlow'], 2, '.', '') . "\"\n";
            $csv .= "\"Gross Profit/Loss\",\"" . number_format($reportData['grossProfitLoss'], 2, '.', '') . "\"\n";
            $csv .= "\"Net Profit/Loss\",\"" . number_format($reportData['netProfitLoss'], 2, '.', '') . "\"\n";
            $csv .= "\"Profit Margin (%)\",\"" . number_format((float) ($reportData['profitMargin'] ?? 0), 2, '.', '') . "\"\n";
            $csv .= "\"Current Balance\",\"" . number_format((float) $business->balance, 2, '.', '') . "\"\n";
            $csv .= "\n";
            $csv .= "Account Activity\n";
            $csv .= "Date,Type,Reference,Party,Direction,Amount\n";

            foreach ($reportData['activityFeed'] as $activity) {
                $csv .= '"' . $activity['date']->format('Y-m-d') . '",';
                $csv .= '"' . $activity['type_label'] . '",';
                $csv .= '"' . $activity['reference'] . '",';
                $csv .= '"' . $activity['party'] . '",';
                $csv .= '"' . $activity['direction'] . '",';
                $csv .= '"' . number_format($activity['amount'], 2, '.', '') . '"' . "\n";
            }

            return response($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);
        }

        return redirect()->back()->with('error', 'Invalid export type');
    }

    public function getImage(Business $business)
    {
        if (!$business->profile_image) {
            abort(404);
        }

        $imageData = $business->profile_image;

        if (is_string($imageData)) {
            $imageData = $imageData;
        }

        $mimeType = 'image/jpeg';
        try {
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $detectedType = finfo_buffer($finfo, $imageData);
                finfo_close($finfo);
                if ($detectedType && strpos($detectedType, 'image/') === 0) {
                    $mimeType = $detectedType;
                }
            }
        } catch (\Exception $e) {
            // Keep fallback mime type.
        }

        return response($imageData)
            ->header('Content-Type', $mimeType)
            ->header('Content-Length', strlen($imageData))
            ->header('Cache-Control', 'public, max-age=31536000');
    }

    private function getBusinessReportData(Business $business, ?string $from = null, ?string $to = null): array
    {
        $purchasesQuery = $business->purchases()->with(['supplier', 'creator']);
        $salesQuery = $business->invoices()->with(['customer', 'creator'])->where('cancellation_status', 'active');
        $incomesQuery = $business->incomes()->with(['customer', 'creator'])->where('amount_received', '>', 0);
        $supplierPaymentsQuery = $business->supplierPayments()->with('supplier');
        $expensesQuery = $business->expenses();

        $this->applyDateRange($purchasesQuery, 'purchase_date', $from, $to);
        $this->applyDateRange($salesQuery, 'invoice_date', $from, $to);
        $this->applyDateRange($incomesQuery, 'transaction_date', $from, $to);
        $this->applyDateRange($supplierPaymentsQuery, 'date', $from, $to);
        $this->applyDateRange($expensesQuery, 'transaction_date', $from, $to);

        $purchases = $purchasesQuery->orderByDesc('purchase_date')->orderByDesc('id')->get();
        $sales = $salesQuery->orderByDesc('invoice_date')->orderByDesc('id')->get();
        $incomes = $incomesQuery->orderByDesc('transaction_date')->orderByDesc('id')->get();
        $supplierPayments = $supplierPaymentsQuery->orderByDesc('date')->orderByDesc('id')->get();

        $purchaseTotal = (float) $purchases->sum('total_cost');
        $salesTotal = (float) $sales->sum('total_cost');
        $incomeTotal = (float) $incomes->sum('amount_received');
        $otherIncomeTotal = (float) $incomes->where('income_type', 'Other')->sum('amount_received');
        $saleIncomeTotal = (float) $incomes->where('income_type', 'Sale')->sum('amount_received');
        $dueCollectionTotal = (float) $incomes->where('income_type', 'Due Collection')->sum('amount_received');
        $supplierPaymentTotal = (float) $supplierPayments->sum('amount');
        $expenseTotal = (float) $expensesQuery->sum('amount');
        $netCashFlow = $incomeTotal - $supplierPaymentTotal;
        // Gross Profit = Sales Revenue − Cost of Goods (purchases)
        $grossProfitLoss = $salesTotal - $purchaseTotal;
        // Net Profit = Gross Profit + Other Income − Operating Expenses
        $netProfitLoss = $grossProfitLoss + $otherIncomeTotal - $expenseTotal;
        $profitMargin = $salesTotal > 0 ? (($netProfitLoss / $salesTotal) * 100) : null;

        $activityFeed = $this->buildActivityFeed($purchases, $sales, $incomes, $supplierPayments);

        return [
            'purchases' => $purchases,
            'sales' => $sales,
            'incomes' => $incomes,
            'supplierPayments' => $supplierPayments,
            'purchaseTotal' => $purchaseTotal,
            'salesTotal' => $salesTotal,
            'incomeTotal' => $incomeTotal,
            'otherIncomeTotal' => $otherIncomeTotal,
            'saleIncomeTotal' => $saleIncomeTotal,
            'dueCollectionTotal' => $dueCollectionTotal,
            'supplierPaymentTotal' => $supplierPaymentTotal,
            'expenseTotal' => $expenseTotal,
            'netCashFlow' => $netCashFlow,
            'grossProfitLoss' => $grossProfitLoss,
            'netProfitLoss' => $netProfitLoss,
            'profitMargin' => $profitMargin,
            'purchaseCount' => $purchases->count(),
            'salesCount' => $sales->count(),
            'incomeCount' => $incomes->count(),
            'supplierPaymentCount' => $supplierPayments->count(),
            'activityFeed' => $activityFeed,
            'paginatedActivityFeed' => $this->paginateCollection($activityFeed, 10, 'activity_page'),
            'recentPurchases' => $this->paginateCollection($purchases, 6, 'purchases_page'),
            'recentSales' => $this->paginateCollection($sales, 6, 'sales_page'),
            'recentIncomes' => $this->paginateCollection($incomes, 6, 'incomes_page'),
            'recentSupplierPayments' => $this->paginateCollection($supplierPayments, 6, 'payments_page'),
        ];
    }

    private function paginateCollection($items, int $perPage, string $pageName): LengthAwarePaginator
    {
        $collection = collect($items)->values();
        $currentPage = LengthAwarePaginator::resolveCurrentPage($pageName);
        $currentItems = $collection->forPage($currentPage, $perPage)->values();

        return new LengthAwarePaginator(
            $currentItems,
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => $pageName,
            ]
        );
    }

    private function buildActivityFeed($purchases, $sales, $incomes, $supplierPayments)
    {
        return collect()
            ->merge($purchases->map(function (Purchase $purchase) {
                return [
                    'date' => Carbon::parse($purchase->purchase_date),
                    'type_label' => 'Purchase',
                    'reference' => $purchase->invoice_no ?: 'PUR-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT),
                    'party' => $purchase->supplier->name ?? 'N/A',
                    'direction' => 'Outflow',
                    'amount' => (float) $purchase->total_cost,
                ];
            }))
            ->merge($sales->map(function (Invoice $invoice) {
                return [
                    'date' => Carbon::parse($invoice->invoice_date),
                    'type_label' => 'Sale',
                    'reference' => $invoice->invoice_no ?: 'INV-' . str_pad($invoice->id, 4, '0', STR_PAD_LEFT),
                    'party' => $invoice->customer->name ?? 'Walk-in Customer',
                    'direction' => 'Inflow',
                    'amount' => (float) $invoice->total_cost,
                ];
            }))
            ->merge($incomes->map(function (Income $income) {
                return [
                    'date' => Carbon::parse($income->transaction_date),
                    'type_label' => 'Income',
                    'reference' => $income->reference_no ?: 'INC-' . str_pad($income->id, 4, '0', STR_PAD_LEFT),
                    'party' => $income->customer->name ?? 'General Income',
                    'direction' => 'Inflow',
                    'amount' => (float) $income->amount_received,
                ];
            }))
            ->merge($supplierPayments->map(function (SupplierPayment $payment) {
                return [
                    'date' => Carbon::parse($payment->date),
                    'type_label' => 'Supplier Payment',
                    'reference' => $payment->payment_reference ?: 'PAY-' . str_pad($payment->id, 4, '0', STR_PAD_LEFT),
                    'party' => $payment->supplier->name ?? 'N/A',
                    'direction' => 'Outflow',
                    'amount' => (float) $payment->amount,
                ];
            }))
            ->sortByDesc('date')
            ->values();
    }

    private function buildChartData(Business $business): array
    {
        return [
            'daily' => $this->buildDailyChartSeries($business),
            'weekly' => $this->buildWeeklyChartSeries($business),
            'monthly' => $this->buildMonthlyChartSeries($business),
            'yearly' => $this->buildYearlyChartSeries($business),
        ];
    }

    private function buildDailyChartSeries(Business $business): array
    {
        $labels = [];
        $purchases = [];
        $sales = [];
        $income = [];
        $payments = [];
        $otherIncome = [];
        $grossProfit = [];
        $netProfit = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $purchaseAmount = (float) $business->purchases()->whereDate('purchase_date', $date)->sum('total_cost');
            $salesAmount = (float) $business->invoices()->where('cancellation_status', 'active')->whereDate('invoice_date', $date)->sum('total_cost');
            $incomeAmount = (float) $business->incomes()->where('amount_received', '>', 0)->whereDate('transaction_date', $date)->sum('amount_received');
            $paymentAmount = (float) $business->supplierPayments()->whereDate('date', $date)->sum('amount');
            $otherIncomeAmount = (float) $business->incomes()
                ->where('amount_received', '>', 0)
                ->where('income_type', 'Other')
                ->whereDate('transaction_date', $date)
                ->sum('amount_received');

            $labels[] = $date->format('M d');
            $purchases[] = $purchaseAmount;
            $sales[] = $salesAmount;
            $income[] = $incomeAmount;
            $payments[] = $paymentAmount;
            $otherIncome[] = $otherIncomeAmount;
            $grossProfit[] = $salesAmount - $purchaseAmount;
            $netProfit[] = $salesAmount + $otherIncomeAmount - $purchaseAmount;
        }

        return compact('labels', 'purchases', 'sales', 'income', 'payments', 'otherIncome', 'grossProfit', 'netProfit');
    }

    private function buildWeeklyChartSeries(Business $business): array
    {
        $labels = [];
        $purchases = [];
        $sales = [];
        $income = [];
        $payments = [];
        $otherIncome = [];
        $grossProfit = [];
        $netProfit = [];

        for ($i = 7; $i >= 0; $i--) {
            $start = Carbon::now()->subWeeks($i)->startOfWeek();
            $end = Carbon::now()->subWeeks($i)->endOfWeek();
            $purchaseAmount = (float) $business->purchases()->whereBetween('purchase_date', [$start, $end])->sum('total_cost');
            $salesAmount = (float) $business->invoices()->where('cancellation_status', 'active')->whereBetween('invoice_date', [$start, $end])->sum('total_cost');
            $incomeAmount = (float) $business->incomes()->where('amount_received', '>', 0)->whereBetween('transaction_date', [$start, $end])->sum('amount_received');
            $paymentAmount = (float) $business->supplierPayments()->whereBetween('date', [$start, $end])->sum('amount');
            $otherIncomeAmount = (float) $business->incomes()
                ->where('amount_received', '>', 0)
                ->where('income_type', 'Other')
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount_received');

            $labels[] = 'Week ' . $start->format('M d');
            $purchases[] = $purchaseAmount;
            $sales[] = $salesAmount;
            $income[] = $incomeAmount;
            $payments[] = $paymentAmount;
            $otherIncome[] = $otherIncomeAmount;
            $grossProfit[] = $salesAmount - $purchaseAmount;
            $netProfit[] = $salesAmount + $otherIncomeAmount - $purchaseAmount;
        }

        return compact('labels', 'purchases', 'sales', 'income', 'payments', 'otherIncome', 'grossProfit', 'netProfit');
    }

    private function buildMonthlyChartSeries(Business $business): array
    {
        $labels = [];
        $purchases = [];
        $sales = [];
        $income = [];
        $payments = [];
        $otherIncome = [];
        $grossProfit = [];
        $netProfit = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $purchaseAmount = (float) $business->purchases()
                ->whereYear('purchase_date', $month->year)
                ->whereMonth('purchase_date', $month->month)
                ->sum('total_cost');
            $salesAmount = (float) $business->invoices()
                ->where('cancellation_status', 'active')
                ->whereYear('invoice_date', $month->year)
                ->whereMonth('invoice_date', $month->month)
                ->sum('total_cost');
            $incomeAmount = (float) $business->incomes()
                ->where('amount_received', '>', 0)
                ->whereYear('transaction_date', $month->year)
                ->whereMonth('transaction_date', $month->month)
                ->sum('amount_received');
            $paymentAmount = (float) $business->supplierPayments()
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('amount');
            $otherIncomeAmount = (float) $business->incomes()
                ->where('amount_received', '>', 0)
                ->where('income_type', 'Other')
                ->whereYear('transaction_date', $month->year)
                ->whereMonth('transaction_date', $month->month)
                ->sum('amount_received');

            $labels[] = $month->format('M Y');
            $purchases[] = $purchaseAmount;
            $sales[] = $salesAmount;
            $income[] = $incomeAmount;
            $payments[] = $paymentAmount;
            $otherIncome[] = $otherIncomeAmount;
            $grossProfit[] = $salesAmount - $purchaseAmount;
            $netProfit[] = $salesAmount + $otherIncomeAmount - $purchaseAmount;
        }

        return compact('labels', 'purchases', 'sales', 'income', 'payments', 'otherIncome', 'grossProfit', 'netProfit');
    }

    private function buildYearlyChartSeries(Business $business): array
    {
        $labels = [];
        $purchases = [];
        $sales = [];
        $income = [];
        $payments = [];
        $otherIncome = [];
        $grossProfit = [];
        $netProfit = [];

        for ($i = 4; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i)->year;
            $purchaseAmount = (float) $business->purchases()->whereYear('purchase_date', $year)->sum('total_cost');
            $salesAmount = (float) $business->invoices()->where('cancellation_status', 'active')->whereYear('invoice_date', $year)->sum('total_cost');
            $incomeAmount = (float) $business->incomes()->where('amount_received', '>', 0)->whereYear('transaction_date', $year)->sum('amount_received');
            $paymentAmount = (float) $business->supplierPayments()->whereYear('date', $year)->sum('amount');
            $otherIncomeAmount = (float) $business->incomes()
                ->where('amount_received', '>', 0)
                ->where('income_type', 'Other')
                ->whereYear('transaction_date', $year)
                ->sum('amount_received');

            $labels[] = (string) $year;
            $purchases[] = $purchaseAmount;
            $sales[] = $salesAmount;
            $income[] = $incomeAmount;
            $payments[] = $paymentAmount;
            $otherIncome[] = $otherIncomeAmount;
            $grossProfit[] = $salesAmount - $purchaseAmount;
            $netProfit[] = $salesAmount + $otherIncomeAmount - $purchaseAmount;
        }

        return compact('labels', 'purchases', 'sales', 'income', 'payments', 'otherIncome', 'grossProfit', 'netProfit');
    }

    private function applyDateRange(Builder|Relation $query, string $column, ?string $from, ?string $to): void
    {
        if ($from) {
            $query->whereDate($column, '>=', $from);
        }

        if ($to) {
            $query->whereDate($column, '<=', $to);
        }
    }

    private function getEcommerceOverviewData(Business $business, ?string $from = null, ?string $to = null): array
    {
        $query = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('ecommerce_products', 'order_items.product_id', '=', 'ecommerce_products.id')
            ->join('products', 'ecommerce_products.product_id', '=', 'products.id')
            ->where('products.business_id', $business->id)
            ->where('orders.payment_status', 'verified')
            ->where('orders.delivery_status', '!=', 'cancelled');

        if ($from) {
            $query->whereDate('orders.created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('orders.created_at', '<=', $to);
        }

        $ecommerceOrders = (clone $query)
            ->selectRaw('orders.id as order_id')
            ->selectRaw('orders.order_number')
            ->selectRaw('orders.customer_name')
            ->selectRaw('orders.payment_method')
            ->selectRaw('orders.payment_status')
            ->selectRaw('orders.delivery_status')
            ->selectRaw('orders.created_at as order_created_at')
            ->selectRaw('SUM(order_items.quantity) as total_units')
            ->selectRaw('SUM(order_items.subtotal) as gross_income')
            ->selectRaw('SUM(COALESCE(ecommerce_products.profit, 0) * order_items.quantity) as estimated_profit')
            ->groupBy([
                'orders.id',
                'orders.order_number',
                'orders.customer_name',
                'orders.payment_method',
                'orders.payment_status',
                'orders.delivery_status',
                'orders.created_at',
            ])
            ->orderByDesc('orders.created_at')
            ->orderByDesc('orders.id')
            ->get();

        return [
            'ecommerceOrdersCount' => $ecommerceOrders->count(),
            'ecommerceUnitsSold' => (int) $ecommerceOrders->sum('total_units'),
            'ecommerceGrossIncome' => (float) $ecommerceOrders->sum('gross_income'),
            'ecommerceEstimatedProfit' => (float) $ecommerceOrders->sum('estimated_profit'),
            'recentEcommerceOrders' => $this->paginateCollection($ecommerceOrders, 6, 'ecommerce_page'),
        ];
    }
}
