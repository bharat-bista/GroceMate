<?php
// app/Http/Controllers/Inventory/DashboardController.php
namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\PurchaseItem;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Imports Carbon for date handling

class DashboardController extends Controller
{
  public function index()
  {
    // 1. DEFINE the variables first
    $today = Carbon::today(); 
    $soonThreshold = $today->copy()->addDays(30);
    
    $totalProducts = Product::count();
    $activeProducts = Product::where('is_active', true)->count();

    $lowStockCount = Stock::whereColumn('quantity', '<=', 'reorder_level')
      ->where('reorder_level', '>', 0)
      ->count();

    $topLowStock = Stock::with('product')
      ->whereColumn('quantity', '<=', 'reorder_level')
      ->where('reorder_level', '>', 0)
      ->orderBy('quantity')
      ->limit(8)
      ->get();
    
      $supplierCount = Supplier::count();
      // 1. Expiring Soon: Not yet expired, but expires within 30 days
        $expiringSoonCount = PurchaseItem::whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$today, $soonThreshold])
            ->count();

        // 2. Expired: Expiry date is before today
        $expiredCount = PurchaseItem::whereNotNull('expiry_date')
            ->where('expiry_date', '<', $today)
            ->count();
    // DAILY (last 7 days - always continuous)
$dailyPurchases = collect();

for ($i = 6; $i >= 0; $i--) {
    $date = Carbon::today()->subDays($i)->format('Y-m-d');

    $total = Purchase::whereDate('purchase_date', $date)
        ->sum('total_cost');

    $dailyPurchases->push((object)[
        'label' => $date,
        'total' => $total
    ]);
}

$weeklyPurchases = collect();

for ($i = 7; $i >= 0; $i--) {
    $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
    $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();

    $total = Purchase::whereBetween('purchase_date', [$startOfWeek, $endOfWeek])
        ->sum('total_cost');

    $weeklyPurchases->push((object)[
        'label' => $startOfWeek->format('M d'),
        'total' => $total
    ]);
}

$monthlyPurchases = collect();

for ($i = 11; $i >= 0; $i--) {
    $month = Carbon::now()->subMonths($i);

    $total = Purchase::whereYear('purchase_date', $month->year)
        ->whereMonth('purchase_date', $month->month)
        ->sum('total_cost');

    $monthlyPurchases->push((object)[
        'label' => $month->format('Y-m'),
        'total' => $total
    ]);
}

$yearlyPurchases = Purchase::select(
    DB::raw('YEAR(purchase_date) as label'),
    DB::raw('SUM(total_cost) as total')
)
->groupBy('label')
->orderBy('label')
->get();
   
return view('inventory.dashboard', compact(
        'totalProducts',
        'activeProducts',
        'lowStockCount',
        'topLowStock',
        'supplierCount',
        'expiringSoonCount',
        'expiredCount',
        'dailyPurchases',
        'weeklyPurchases',
        'monthlyPurchases',
        'yearlyPurchases'
    ));
  }
}

