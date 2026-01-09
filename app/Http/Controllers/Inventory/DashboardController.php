<?php
// app/Http/Controllers/Inventory/DashboardController.php
namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\PurchaseItem;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
  public function index()
  {
    // 1. DEFINE the variables first
    $today = \Carbon\Carbon::today(); 
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
    return view('inventory.dashboard', compact(
      'totalProducts','activeProducts','lowStockCount','topLowStock','supplierCount','expiringSoonCount','expiredCount'
    ));
  }
}

