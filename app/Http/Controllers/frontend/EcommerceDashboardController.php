<?php

namespace App\Http\Controllers\frontend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderRefund;
use App\Models\EcommerceProduct;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class EcommerceDashboardController extends Controller
{
    /**
     * Display the ecommerce dashboard with comprehensive statistics.
     */
    public function index(Request $request)
    {
        // Get date ranges
        $today = now();
        $thisMonthStart = now()->startOfMonth();
        $thisMonthEnd = now()->endOfMonth();

        // Today's Orders
        $todayOrders = Order::whereDate('created_at', $today)->count();
        
        // This Month Orders
        $thisMonthOrders = Order::whereBetween('created_at', [$thisMonthStart, $thisMonthEnd])->count();
        
        // Today's Revenue (net = gross paid - completed refunds)
        $todayRevenue = $this->netRevenueBetween($today, $today);
        
        // This Month Revenue (net = gross paid - completed refunds)
        $thisMonthRevenue = $this->netRevenueBetween($thisMonthStart, $thisMonthEnd);
        
        // Total Revenue (net = gross paid - completed refunds)
        $totalRevenue = $this->netRevenueAllTime();
        
        // Pending Orders
        $pendingOrders = Order::where('delivery_status', 'pending')->count();
        
        // Processing Orders
        $processingOrders = Order::where('delivery_status', 'processing')->count();
        
        // Shipped Orders
        $shippedOrders = Order::where('delivery_status', 'shipped')->count();
        
        // Delivered Orders
        $deliveredOrders = Order::where('delivery_status', 'delivered')->count();
        
        // Cancelled Orders
        $cancelledOrders = Order::where('delivery_status', 'cancelled')->count();
        
        // Unpaid Orders
        $unpaidOrders = Order::whereIn('payment_status', ['pending', 'failed', 'cod'])->count();
        
        // Total Products
        $totalProducts = EcommerceProduct::latestPerProduct()->count();
        
        // Low Stock Products (less than 10)
        $lowStockProducts = EcommerceProduct::latestPerProduct()
            ->where('ecommerce_stock', '<', 10)
            ->count();
        
        // Chart Data
        $chartData = $this->getChartData();

        // Recent Orders (active)
        $recentOrders = $this->getRecentOrders();

        // Recent Cancelled Orders
        $recentCancelledOrders = $this->getRecentCancelledOrders();
        
        // Order Status Breakdown
        $orderStatusBreakdown = [
            'pending' => $pendingOrders,
            'processing' => $processingOrders,
            'shipped' => $shippedOrders,
            'delivered' => $deliveredOrders,
            'cancelled' => $cancelledOrders,
        ];
        
        // Payment Status Breakdown
        $paymentStatusBreakdown = [
            'paid' => (clone $this->grossPaidOrdersQuery())->count(),
            'unpaid' => $unpaidOrders,
        ];

        // Quick Stats
        $refundSummary = $this->getRefundSummary();
        $refunds = $this->getRefunds();

        $quickStats = [
            'today_orders' => $todayOrders,
            'this_month_orders' => $thisMonthOrders,
            'today_revenue' => $todayRevenue,
            'this_month_revenue' => $thisMonthRevenue,
            'total_revenue' => $totalRevenue,
            'pending_orders' => $pendingOrders,
            'processing_orders' => $processingOrders,
            'shipped_orders' => $shippedOrders,
            'delivered_orders' => $deliveredOrders,
            'cancelled_orders' => $cancelledOrders,
            'unpaid_orders' => $unpaidOrders,
            'total_products' => $totalProducts,
            'low_stock_products' => $lowStockProducts,
        ];

        return view('inventory.ecommerce.dashboard', compact(
            'quickStats',
            'chartData',
            'recentOrders',
            'recentCancelledOrders',
            'orderStatusBreakdown',
            'paymentStatusBreakdown',
            'refundSummary',
            'refunds'
        ));
    }

    private function grossPaidOrdersQuery(): Builder
    {
        // Excludes cancelled orders — so no separate refund deduction is needed in revenue helpers.
        return Order::query()
            ->where('payment_status', 'verified')
            ->where('delivery_status', '!=', 'cancelled');
    }

    /**
     * Get chart data for orders and revenue (all time periods)
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
        $ordersData = [];
        $revenueData = [];

        // Get last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('M d');
            
            // Get active orders count for this day
            $ordersCount = Order::whereDate('created_at', $date)
                ->where('delivery_status', '!=', 'cancelled')
                ->count();
            $ordersData[] = $ordersCount;
            
            $revenueTotal = $this->netRevenueBetween($date, $date);
            $revenueData[] = $revenueTotal;
        }

        return [
            'labels' => $days,
            'orders' => $ordersData,
            'revenue' => $revenueData
        ];
    }

    /**
     * Get weekly data for last 12 weeks
     */
    private function getWeeklyData()
    {
        $weeks = [];
        $ordersData = [];
        $revenueData = [];

        // Get last 12 weeks
        for ($i = 11; $i >= 0; $i--) {
            $weekStart = now()->subWeeks($i)->startOfWeek();
            $weekEnd = now()->subWeeks($i)->endOfWeek();
            
            $weeks[] = "Week " . $weekStart->format('m/d') . "-" . $weekEnd->format('m/d');
            
            // Get active orders count for this week
            $ordersCount = Order::whereBetween('created_at', [$weekStart, $weekEnd])
                ->where('delivery_status', '!=', 'cancelled')
                ->count();
            $ordersData[] = $ordersCount;
            
            $revenueTotal = $this->netRevenueBetween($weekStart, $weekEnd);
            $revenueData[] = $revenueTotal;
        }

        return [
            'labels' => $weeks,
            'orders' => $ordersData,
            'revenue' => $revenueData
        ];
    }

    /**
     * Get monthly data for last 6 months
     */
    private function getMonthlyData()
    {
        $months = [];
        $ordersData = [];
        $revenueData = [];

        // Get last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();
            
            $months[] = $month->format('M Y');
            
            // Get active orders count for this month
            $ordersCount = Order::whereBetween('created_at', [$monthStart, $monthEnd])
                ->where('delivery_status', '!=', 'cancelled')
                ->count();
            $ordersData[] = $ordersCount;
            
            $revenueTotal = $this->netRevenueBetween($monthStart, $monthEnd);
            $revenueData[] = $revenueTotal;
        }

        return [
            'labels' => $months,
            'orders' => $ordersData,
            'revenue' => $revenueData
        ];
    }

    /**
     * Get yearly data for last 5 years
     */
    private function getYearlyData()
    {
        $years = [];
        $ordersData = [];
        $revenueData = [];

        // Get last 5 years
        for ($i = 4; $i >= 0; $i--) {
            $year = now()->subYears($i);
            $yearStart = $year->copy()->startOfYear();
            $yearEnd = $year->copy()->endOfYear();
            
            $years[] = $year->format('Y');
            
            // Get active orders count for this year
            $ordersCount = Order::whereBetween('created_at', [$yearStart, $yearEnd])
                ->where('delivery_status', '!=', 'cancelled')
                ->count();
            $ordersData[] = $ordersCount;
            
            $revenueTotal = $this->netRevenueBetween($yearStart, $yearEnd);
            $revenueData[] = $revenueTotal;
        }

        return [
            'labels' => $years,
            'orders' => $ordersData,
            'revenue' => $revenueData
        ];
    }

    /**
     * Get recent orders with status information
     */
    private function getRecentOrders()
    {
        return Order::orderBy('created_at', 'desc')
            ->where('delivery_status', '!=', 'cancelled')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'total_amount' => $order->total_amount,
                    'payment_status' => $order->payment_status,
                    'delivery_status' => $order->delivery_status,
                    'created_at' => $order->created_at,
                ];
            });
    }

    private function getRecentCancelledOrders()
    {
        return Order::where('delivery_status', 'cancelled')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'total_amount' => $order->total_amount,
                    'payment_status' => $order->payment_status,
                    'created_at' => $order->created_at,
                ];
            });
    }

    private function netRevenueBetween(Carbon|string $from, Carbon|string $to): float
    {
        $fromDate = $from instanceof Carbon ? $from->copy()->startOfDay() : Carbon::parse($from)->startOfDay();
        $toDate = $to instanceof Carbon ? $to->copy()->endOfDay() : Carbon::parse($to)->endOfDay();

        return (float) (clone $this->grossPaidOrdersQuery())
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->sum('total_amount');
    }

    private function netRevenueAllTime(): float
    {
        return (float) (clone $this->grossPaidOrdersQuery())->sum('total_amount');
    }

    private function getRefundSummary(): array
    {
        return [
            'pending_amount' => (float) OrderRefund::where('refund_status', 'pending')->sum('refund_amount'),
            'completed_amount' => (float) OrderRefund::where('refund_status', 'completed')->sum('refund_amount'),
            'total_refunds' => (int) OrderRefund::count(),
        ];
    }

    private function getRefunds()
    {
        return OrderRefund::with(['order.items'])
            ->orderByDesc('created_at')
            ->get();
    }
}
