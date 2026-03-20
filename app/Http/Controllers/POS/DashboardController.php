<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\POS\Customer;
use App\Models\POS\Invoice;
use App\Models\POS\Income;
use App\Models\SupplierPayment;

class DashboardController extends Controller
{
    /**
     * Display the POS dashboard with comprehensive statistics.
     */
    public function index(Request $request)
    {
        // Get date ranges
        $today = now()->format('Y-m-d');
        $thisMonthStart = now()->startOfMonth()->format('Y-m-d');
        $thisMonthEnd = now()->endOfMonth()->format('Y-m-d');

        // Today's Sales
        $todaySales = Invoice::whereDate('created_at', $today)->sum('total_cost');
        
        // This Month Sales
        $thisMonthSales = Invoice::whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])->sum('total_cost');
        
        // Total Orders Today
        $todayOrders = Invoice::whereDate('created_at', $today)->count();
        
        // Total Customers
        $totalCustomers = Customer::count();
        
        // Total Due (from customers)
        $totalDue = Customer::sum('total_due');
        
        // Total Income Received
        $totalIncome = Income::sum('amount_received');
        
        // Income Received Today
        $todayIncome = Income::whereDate('transaction_date', $today)->sum('amount_received');
        
        // Income Received This Month
        $thisMonthIncome = Income::whereBetween('transaction_date', [$thisMonthStart, $thisMonthEnd])->sum('amount_received');

        // Chart Data (All time periods)
        $chartData = $this->getChartData();

        // Recent Transactions
        $recentTransactions = $this->getRecentTransactions();

        // Quick Actions Data
        $quickStats = [
            'today_sales' => $todaySales,
            'this_month_sales' => $thisMonthSales,
            'today_orders' => $todayOrders,
            'total_customers' => $totalCustomers,
            'total_due' => $totalDue,
            'total_income' => $totalIncome,
            'today_income' => $todayIncome,
            'this_month_income' => $thisMonthIncome,
        ];

        return view('pos.dashboard.index', compact('quickStats', 'chartData', 'recentTransactions'));
    }

    /**
     * Get chart data for sales and income (all time periods)
     */
    private function getChartData()
    {
        return [
            'daily' => $this->getDailyData(),
            'weekly' => $this->getWeeklyData(),
            'monthly' => $this->getMonthlyData(),
            'yearly' => $this->getYearlyData()
        ];
    }

    /**
     * Get daily data for last 30 days
     */
    private function getDailyData()
    {
        $days = [];
        $salesData = [];
        $incomeData = [];

        // Get last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('M d');
            
            // Get sales data for this day
            $salesTotal = Invoice::whereDate('created_at', $date)
                ->sum('total_cost');
            $salesData[] = $salesTotal;
            
            // Get income data for this day
            $incomeTotal = Income::whereDate('transaction_date', $date)
                ->sum('amount_received');
            $incomeData[] = $incomeTotal;
        }

        return [
            'labels' => $days,
            'sales' => $salesData,
            'income' => $incomeData
        ];
    }

    /**
     * Get weekly data for last 12 weeks
     */
    private function getWeeklyData()
    {
        $weeks = [];
        $salesData = [];
        $incomeData = [];

        // Get last 12 weeks
        for ($i = 11; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            
            $weeks[] = "Week " . $weekStart->format('m/d') . "-" . $weekEnd->format('m/d');
            
            // Get sales data for this week
            $salesTotal = Invoice::whereBetween('created_at', [$weekStart, $weekEnd])
                ->sum('total_cost');
            $salesData[] = $salesTotal;
            
            // Get income data for this week
            $incomeTotal = Income::whereBetween('transaction_date', [$weekStart, $weekEnd])
                ->sum('amount_received');
            $incomeData[] = $incomeTotal;
        }

        return [
            'labels' => $weeks,
            'sales' => $salesData,
            'income' => $incomeData
        ];
    }

    /**
     * Get monthly data for last 6 months
     */
    private function getMonthlyData()
    {
        $months = [];
        $salesData = [];
        $incomeData = [];

        // Get last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $months[] = $month->format('M Y');
            
            // Get sales data for this month
            $salesTotal = Invoice::whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_cost');
            $salesData[] = $salesTotal;
            
            // Get income data for this month
            $incomeTotal = Income::whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->sum('amount_received');
            $incomeData[] = $incomeTotal;
        }

        return [
            'labels' => $months,
            'sales' => $salesData,
            'income' => $incomeData
        ];
    }

    /**
     * Get yearly data for last 5 years
     */
    private function getYearlyData()
    {
        $years = [];
        $salesData = [];
        $incomeData = [];

        // Get last 5 years
        for ($i = 4; $i >= 0; $i--) {
            $year = now()->subYears($i);
            $yearStart = $year->copy()->startOfYear();
            $yearEnd = $year->copy()->endOfYear();
            
            $years[] = $year->format('Y');
            
            // Get sales data for this year
            $salesTotal = Invoice::whereBetween('created_at', [$yearStart, $yearEnd])
                ->sum('total_cost');
            $salesData[] = $salesTotal;
            
            // Get income data for this year
            $incomeTotal = Income::whereBetween('transaction_date', [$yearStart, $yearEnd])
                ->sum('amount_received');
            $incomeData[] = $incomeTotal;
        }

        return [
            'labels' => $years,
            'sales' => $salesData,
            'income' => $incomeData
        ];
    }

    /**
     * Get recent transactions (customers and suppliers)
     */
    private function getRecentTransactions()
    {
        $transactions = collect();

        // Recent customer payments (incomes)
        $recentIncomes = Income::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($income) {
                return [
                    'id' => $income->id,
                    'date' => $income->transaction_date,
                    'description' => 'Payment from ' . ($income->customer->name ?? 'Unknown'),
                    'amount' => $income->amount_received,
                    'type' => 'income',
                    'reference' => $income->reference_no ?? 'INC-' . str_pad($income->id, 4, '0', STR_PAD_LEFT),
                    'created_at' => $income->created_at
                ];
            });

        // Recent supplier payments
        $recentSupplierPayments = SupplierPayment::with('supplier')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'date' => $payment->payment_date,
                    'description' => 'Payment to ' . ($payment->supplier->name ?? 'Unknown'),
                    'amount' => $payment->paid_amount,
                    'type' => 'payment',
                    'reference' => $payment->reference_no ?? 'PAY-' . str_pad($payment->id, 4, '0', STR_PAD_LEFT),
                    'created_at' => $payment->created_at
                ];
            });

        // Recent sales (invoices)
        $recentSales = Invoice::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'date' => $invoice->created_at->format('Y-m-d'),
                    'description' => 'Sale to ' . ($invoice->customer->name ?? 'Walk-in Customer'),
                    'amount' => $invoice->total_cost,
                    'type' => 'sale',
                    'reference' => $invoice->invoice_no,
                    'created_at' => $invoice->created_at
                ];
            });

        // Merge and sort by created_at
        $allTransactions = $transactions
            ->merge($recentIncomes)
            ->merge($recentSupplierPayments)
            ->merge($recentSales)
            ->sortByDesc('created_at')
            ->take(10)
            ->values();

        return $allTransactions;
    }
}
