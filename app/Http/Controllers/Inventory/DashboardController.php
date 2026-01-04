<?php
// app/Http/Controllers/Inventory/DashboardController.php
namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
  public function index()
  {
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

    return view('inventory.dashboard', compact(
      'totalProducts','activeProducts','lowStockCount','topLowStock'
    ));
  }
}

