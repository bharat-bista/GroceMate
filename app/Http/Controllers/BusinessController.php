<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Purchase;
use App\Models\SupplierPayment;
use App\Models\POS\Income;
use App\Models\POS\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function index()
    {
        $businesses = Business::latest()->get();
        return view('pos.business.index', compact('businesses'));
    }

    public function show(Business $business)
    {
        $reportData = $this->getBusinessReportData($business);

        return view('pos.business.show', array_merge($reportData, [
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
            $csv .= "\"Supplier Payments\",\"" . number_format($reportData['supplierPaymentTotal'], 2, '.', '') . "\"\n";
            $csv .= "\"Net Cash Flow\",\"" . number_format($reportData['netCashFlow'], 2, '.', '') . "\"\n";
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
        $salesQuery = $business->invoices()->with(['customer', 'creator']);
        $incomesQuery = $business->incomes()->with(['customer', 'creator'])->where('amount_received', '>', 0);
        $supplierPaymentsQuery = $business->supplierPayments()->with('supplier');

        $this->applyDateRange($purchasesQuery, 'purchase_date', $from, $to);
        $this->applyDateRange($salesQuery, 'invoice_date', $from, $to);
        $this->applyDateRange($incomesQuery, 'transaction_date', $from, $to);
        $this->applyDateRange($supplierPaymentsQuery, 'date', $from, $to);

        $purchases = $purchasesQuery->orderByDesc('purchase_date')->orderByDesc('id')->get();
        $sales = $salesQuery->orderByDesc('invoice_date')->orderByDesc('id')->get();
        $incomes = $incomesQuery->orderByDesc('transaction_date')->orderByDesc('id')->get();
        $supplierPayments = $supplierPaymentsQuery->orderByDesc('date')->orderByDesc('id')->get();

        $purchaseTotal = (float) $purchases->sum('total_cost');
        $salesTotal = (float) $sales->sum('total_cost');
        $incomeTotal = (float) $incomes->sum('amount_received');
        $supplierPaymentTotal = (float) $supplierPayments->sum('amount');
        $netCashFlow = $incomeTotal - $supplierPaymentTotal;

        $activityFeed = $this->buildActivityFeed($purchases, $sales, $incomes, $supplierPayments);

        return [
            'purchases' => $purchases,
            'sales' => $sales,
            'incomes' => $incomes,
            'supplierPayments' => $supplierPayments,
            'purchaseTotal' => $purchaseTotal,
            'salesTotal' => $salesTotal,
            'incomeTotal' => $incomeTotal,
            'supplierPaymentTotal' => $supplierPaymentTotal,
            'netCashFlow' => $netCashFlow,
            'purchaseCount' => $purchases->count(),
            'salesCount' => $sales->count(),
            'incomeCount' => $incomes->count(),
            'supplierPaymentCount' => $supplierPayments->count(),
            'activityFeed' => $activityFeed,
            'recentPurchases' => $purchases->take(5),
            'recentSales' => $sales->take(5),
            'recentIncomes' => $incomes->take(5),
            'recentSupplierPayments' => $supplierPayments->take(5),
        ];
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

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('M d');
            $purchases[] = (float) $business->purchases()->whereDate('purchase_date', $date)->sum('total_cost');
            $sales[] = (float) $business->invoices()->whereDate('invoice_date', $date)->sum('total_cost');
            $income[] = (float) $business->incomes()->where('amount_received', '>', 0)->whereDate('transaction_date', $date)->sum('amount_received');
            $payments[] = (float) $business->supplierPayments()->whereDate('date', $date)->sum('amount');
        }

        return compact('labels', 'purchases', 'sales', 'income', 'payments');
    }

    private function buildWeeklyChartSeries(Business $business): array
    {
        $labels = [];
        $purchases = [];
        $sales = [];
        $income = [];
        $payments = [];

        for ($i = 7; $i >= 0; $i--) {
            $start = Carbon::now()->subWeeks($i)->startOfWeek();
            $end = Carbon::now()->subWeeks($i)->endOfWeek();
            $labels[] = 'Week ' . $start->format('M d');
            $purchases[] = (float) $business->purchases()->whereBetween('purchase_date', [$start, $end])->sum('total_cost');
            $sales[] = (float) $business->invoices()->whereBetween('invoice_date', [$start, $end])->sum('total_cost');
            $income[] = (float) $business->incomes()->where('amount_received', '>', 0)->whereBetween('transaction_date', [$start, $end])->sum('amount_received');
            $payments[] = (float) $business->supplierPayments()->whereBetween('date', [$start, $end])->sum('amount');
        }

        return compact('labels', 'purchases', 'sales', 'income', 'payments');
    }

    private function buildMonthlyChartSeries(Business $business): array
    {
        $labels = [];
        $purchases = [];
        $sales = [];
        $income = [];
        $payments = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');
            $purchases[] = (float) $business->purchases()
                ->whereYear('purchase_date', $month->year)
                ->whereMonth('purchase_date', $month->month)
                ->sum('total_cost');
            $sales[] = (float) $business->invoices()
                ->whereYear('invoice_date', $month->year)
                ->whereMonth('invoice_date', $month->month)
                ->sum('total_cost');
            $income[] = (float) $business->incomes()
                ->where('amount_received', '>', 0)
                ->whereYear('transaction_date', $month->year)
                ->whereMonth('transaction_date', $month->month)
                ->sum('amount_received');
            $payments[] = (float) $business->supplierPayments()
                ->whereYear('date', $month->year)
                ->whereMonth('date', $month->month)
                ->sum('amount');
        }

        return compact('labels', 'purchases', 'sales', 'income', 'payments');
    }

    private function buildYearlyChartSeries(Business $business): array
    {
        $labels = [];
        $purchases = [];
        $sales = [];
        $income = [];
        $payments = [];

        for ($i = 4; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i)->year;
            $labels[] = (string) $year;
            $purchases[] = (float) $business->purchases()->whereYear('purchase_date', $year)->sum('total_cost');
            $sales[] = (float) $business->invoices()->whereYear('invoice_date', $year)->sum('total_cost');
            $income[] = (float) $business->incomes()->where('amount_received', '>', 0)->whereYear('transaction_date', $year)->sum('amount_received');
            $payments[] = (float) $business->supplierPayments()->whereYear('date', $year)->sum('amount');
        }

        return compact('labels', 'purchases', 'sales', 'income', 'payments');
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
}
