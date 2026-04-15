<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EcommerceIncomeController extends Controller
{
    public function index(Request $request)
    {
        $incomes = $this->buildIncomeRowsQuery($request)
            ->orderByDesc('order_created_at')
            ->orderByDesc('order_id')
            ->paginate(15)
            ->withQueryString();

        $totalIncome = (clone $this->settledOrdersQuery())->sum('total_amount');

        $thisMonthIncome = (clone $this->settledOrdersQuery())
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');

        $todayIncome = (clone $this->settledOrdersQuery())
            ->whereDate('created_at', today())
            ->sum('total_amount');

        $estimatedProfit = (clone $this->settledItemsQuery())
            ->sum(DB::raw('COALESCE(ecommerce_products.profit, 0) * order_items.quantity'));

        $businessIncomeStats = $this->monthlyBusinessIncomeStats();

        $paymentStats = (clone $this->settledOrdersQuery())
            ->select(
                'payment_method',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        $businesses = Business::query()
            ->orderBy('business_name')
            ->get(['id', 'business_name']);

        return view('frontend.income.index', compact(
            'incomes',
            'totalIncome',
            'thisMonthIncome',
            'todayIncome',
            'estimatedProfit',
            'businessIncomeStats',
            'paymentStats',
            'businesses'
        ));
    }

    public function export(string $type, Request $request)
    {
        $rows = $this->buildIncomeRowsQuery($request)
            ->orderByDesc('order_created_at')
            ->orderByDesc('order_id')
            ->get();

        $from = $request->get('from', $request->get('date_from'));
        $to = $request->get('to', $request->get('date_to'));

        if ($type === 'pdf') {
            $filename = 'ecommerce_income_' . now()->format('Y-m-d') . '.pdf';

            $html = view('frontend.income.export-pdf', [
                'incomes' => $rows,
                'from' => $from,
                'to' => $to,
            ])->render();

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
            $filename = 'ecommerce_income_' . now()->format('Y-m-d') . '.xlsx';

            $html = view('frontend.income.export-excel', [
                'incomes' => $rows,
                'from' => $from,
                'to' => $to,
            ])->render();

            return response($html, 200, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);
        }

        if ($type === 'csv') {
            $filename = 'ecommerce_income_' . now()->format('Y-m-d') . '.csv';

            $csv = "Date,Order #,Customer,Business,Units,Gross Income,Estimated Profit,Payment Method,Delivery Status\n";

            foreach ($rows as $row) {
                $csv .= '"' . \Carbon\Carbon::parse($row->order_created_at)->format('Y-m-d') . '",';
                $csv .= '"' . $row->order_number . '",';
                $csv .= '"' . $row->customer_name . '",';
                $csv .= '"' . ($row->business_name ?? 'Unassigned') . '",';
                $csv .= (int) $row->total_units . ',';
                $csv .= number_format((float) $row->business_gross_income, 2, '.', '') . ',';
                $csv .= number_format((float) $row->estimated_profit, 2, '.', '') . ',';
                $csv .= '"' . strtoupper((string) $row->payment_method) . '",';
                $csv .= '"' . ucfirst((string) $row->delivery_status) . '"' . "\n";
            }

            return response($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);
        }

        return redirect()->back()->with('error', 'Invalid export type');
    }

    private function buildIncomeRowsQuery(Request $request): Builder
    {
        $query = $this->settledItemsQuery()
            ->selectRaw('orders.id as order_id')
            ->selectRaw('orders.order_number')
            ->selectRaw('orders.customer_name')
            ->selectRaw('orders.customer_phone')
            ->selectRaw('orders.payment_method')
            ->selectRaw('orders.payment_status')
            ->selectRaw('orders.delivery_status')
            ->selectRaw('orders.created_at as order_created_at')
            ->selectRaw('businesses.id as business_id')
            ->selectRaw('businesses.business_name as business_name')
            ->selectRaw('businesses.business_type as business_type')
            ->selectRaw('SUM(order_items.quantity) as total_units')
            ->selectRaw('SUM(order_items.subtotal) as business_gross_income')
            ->selectRaw('SUM(COALESCE(ecommerce_products.profit, 0) * order_items.quantity) as estimated_profit')
            ->groupBy([
                'orders.id',
                'orders.order_number',
                'orders.customer_name',
                'orders.customer_phone',
                'orders.payment_method',
                'orders.payment_status',
                'orders.delivery_status',
                'orders.created_at',
                'businesses.id',
                'businesses.business_name',
                'businesses.business_type',
            ]);

        $search = trim((string) $request->get('q', ''));
        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('orders.order_number', 'like', '%' . $search . '%')
                    ->orWhere('orders.customer_name', 'like', '%' . $search . '%')
                    ->orWhere('orders.customer_phone', 'like', '%' . $search . '%')
                    ->orWhere('businesses.business_name', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('business_id')) {
            $query->where('businesses.id', (int) $request->business_id);
        }

        if ($request->filled('payment_method')) {
            $query->where('orders.payment_method', (string) $request->payment_method);
        }

        $from = $request->get('from', $request->get('date_from'));
        if (!empty($from)) {
            $query->whereDate('orders.created_at', '>=', $from);
        }

        $to = $request->get('to', $request->get('date_to'));
        if (!empty($to)) {
            $query->whereDate('orders.created_at', '<=', $to);
        }

        return $query;
    }

    private function settledOrdersQuery(): Builder
    {
        return DB::table('orders')
            ->where(function ($query) {
                $query->where('payment_method', 'esewa')
                    ->orWhere('payment_status', 'verified');
            })
            ->where('delivery_status', '!=', 'cancelled');
    }

    private function settledItemsQuery(): Builder
    {
        return DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('ecommerce_products', 'order_items.product_id', '=', 'ecommerce_products.id')
            ->leftJoin('products', 'ecommerce_products.product_id', '=', 'products.id')
            ->leftJoin('businesses', 'products.business_id', '=', 'businesses.id')
            ->where(function ($query) {
                $query->where('orders.payment_method', 'esewa')
                    ->orWhere('orders.payment_status', 'verified');
            })
            ->where('orders.delivery_status', '!=', 'cancelled');
    }

    private function monthlyBusinessIncomeStats()
    {
        $stats = $this->settledItemsQuery()
            ->whereMonth('orders.created_at', now()->month)
            ->whereYear('orders.created_at', now()->year)
            ->whereNotNull('businesses.id')
            ->selectRaw('businesses.id as business_id')
            ->selectRaw('businesses.business_name')
            ->selectRaw('businesses.business_type')
            ->selectRaw('COUNT(DISTINCT orders.id) as orders_count')
            ->selectRaw('SUM(order_items.subtotal) as total_income')
            ->selectRaw('SUM(COALESCE(ecommerce_products.profit, 0) * order_items.quantity) as estimated_profit')
            ->groupBy('businesses.id', 'businesses.business_name', 'businesses.business_type')
            ->orderByDesc('total_income')
            ->get();

        $balances = Business::query()
            ->whereIn('id', $stats->pluck('business_id')->all())
            ->pluck('balance', 'id');

        $stats->each(function ($row) use ($balances) {
            $row->current_balance = (float) ($balances[$row->business_id] ?? 0);
        });

        return $stats;
    }
}
