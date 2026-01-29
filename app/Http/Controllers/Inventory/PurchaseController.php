<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Stock;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $purchases = Purchase::query()
            ->with(['supplier','creator'])
            ->when($q, function ($qq) use ($q) {
                $qq->where('invoice_no','like',"%{$q}%")
                   ->orWhereHas('supplier', fn($s) => $s->where('name','like',"%{$q}%"));
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('inventory.purchases.index', compact('purchases','q'));
    }

    public function create()
{
    // Get products
    $products = Product::with('stock')->orderBy('name')->get();
    
    // Get last purchase prices for each product
    $productIds = $products->pluck('id')->toArray();
    
    // Get the latest purchase item for each product
    $lastPurchaseItems = PurchaseItem::whereIn('product_id', $productIds)
        ->selectRaw('MAX(id) as id, product_id')
        ->groupBy('product_id')
        ->get()
        ->keyBy('product_id');
    
    // Get the actual purchase items with their costs
    $lastPurchaseItemsWithCosts = PurchaseItem::whereIn('id', $lastPurchaseItems->pluck('id'))
        ->get()
        ->keyBy('product_id');
    
    // Map products with their last purchase cost
    $products = $products->map(function($product) use ($lastPurchaseItemsWithCosts) {
        $lastPurchaseItem = $lastPurchaseItemsWithCosts[$product->id] ?? null;
        
        return [
            'id' => $product->id,
            'name' => $product->name,
            'unit' => $product->unit,
            'sku' => $product->sku,
            'last_cost' => $lastPurchaseItem ? $lastPurchaseItem->unit_cost : 0
        ];
    });

    return view('inventory.purchases.create', [
        'suppliers' => Supplier::orderBy('name')->get(),
        'products'  => $products,
        'taxes'     => Tax::orderBy('name')->get(),
    ]);
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'invoice_no' => ['nullable', 'string', 'max:100'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'], // Can be null for new products
            'items.*.product_name' => ['required', 'string', 'max:255'], // Product name (existing or new)
            'items.*.product_unit' => ['required', 'in:kg,liter,pcs,cartoon,peti,bori,box,bottle,pack,set'], // Unit for new products
            'items.*.qty' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'items.*.expiry_date' => ['nullable', 'date'],
            'items.*.taxes' => ['nullable', 'array'],
            'items.*.taxes.*' => ['nullable', 'integer', 'exists:taxes,id'],
        ]);

        DB::transaction(function () use ($data) {
            // Create purchase header
            $purchase = Purchase::create([
                'supplier_id' => $data['supplier_id'],
                'created_by' => auth()->id(),
                'purchase_date' => $data['purchase_date'],
                'invoice_no' => $data['invoice_no'] ?? null,
                'total_cost' => 0,
            ]);

            $purchaseTotal = 0;

            foreach ($data['items'] as $row) {
                $qty = (float) $row['qty'];
                $unitCost = (float) $row['unit_cost'];
                $baseCost = $qty * $unitCost;
                $taxTotal = 0;

                // Check if product exists, if not create it
                if (!empty($row['product_id'])) {
                    // Existing product
                    $productId = $row['product_id'];
                } else {
                    // Create new product
                    $product = Product::create([
                        'name' => $row['product_name'],
                        'unit' => $row['product_unit'],
                        'category_id' => 1, // Default category, you might want to change this
                        'selling_price' => $unitCost * 1.2, // 20% markup by default
                        'is_active' => true,
                    ]);

                    // Create stock record
                    Stock::create([
                        'product_id' => $product->id,
                        'quantity' => 0,
                        'reorder_level' => 0,
                    ]);

                    $productId = $product->id;
                }

                // Create purchase item
                $item = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $productId,
                    'product_name' => $row['product_name'],         // snapshot
                    'unit' => $row['product_unit'],  
                    'qty' => $qty,
                    'unit_cost' => $unitCost,
                    'base_cost' => $baseCost,
                    'tax_total' => 0,
                    'line_total' => 0,
                    'expiry_date' => $row['expiry_date'] ?? null,
                ]);

                // Apply taxes if any
                if (!empty($row['taxes'])) {
                    $taxes = Tax::whereIn('id', $row['taxes'])->get();

                    foreach ($taxes as $tax) {
                        if ($tax->type === 'fixed') {
                            $taxAmount = $tax->rate * $qty;
                        } else {
                            $taxAmount = ($baseCost * $tax->rate) / 100;
                        }

                        $taxTotal += $taxAmount;
                        $item->taxes()->attach($tax->id, ['tax_amount' => $taxAmount]);
                    }

                    $item->update([
                        'tax_total' => $taxTotal,
                        'line_total' => $baseCost + $taxTotal
                    ]);
                } else {
                    $item->update(['line_total' => $baseCost]);
                }

                // Add to purchase total
                $purchaseTotal += $item->line_total;

                // Update stock
                $stock = Stock::firstOrCreate(
                    ['product_id' => $productId],
                    ['quantity' => 0, 'reorder_level' => 0]
                );
                $stock->increment('quantity', $qty);
            }

            // Update purchase total
            $purchase->update(['total_cost' => $purchaseTotal]);
        });

        return redirect()->route('inventory.purchases.index')
            ->with('success', 'Purchase saved and stock updated.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'creator', 'items.product', 'items.taxes']);
        return view('inventory.purchases.show', compact('purchase'));
    }

    public function expiryAlerts(Request $request)
    {
        $days = (int)($request->get('days', 30));
        if ($days < 1) $days = 30;

        $today = Carbon::today();
        $soon = $today->copy()->addDays($days);

        $expiringSoon = PurchaseItem::query()
            ->with('product')
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$today, $soon])
            ->orderBy('expiry_date')
            ->paginate(10, ['*'], 'soon_page')
            ->withQueryString();

        $expired = PurchaseItem::query()
            ->with('product')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', $today)
            ->orderByDesc('expiry_date')
            ->paginate(10, ['*'], 'expired_page')
            ->withQueryString();

        return view('inventory.alerts.expiry', compact('days','expiringSoon','expired'));
    }
public function searchProducts(Request $request)
{
    $q = trim($request->get('q', ''));
    if (strlen($q) < 2) return response()->json([]);

    // Debug: Log the search query
    \Log::info('Searching for products with query: ' . $q);

    // Simple direct search on purchase items
    $items = PurchaseItem::query()
        ->whereNotNull('product_name')
        ->where('product_name', 'LIKE', '%' . $q . '%')
        ->orderByDesc('id')
        ->get();

    // Debug: Log how many items found
    \Log::info('Found ' . $items->count() . ' items for query: ' . $q);

    $results = $items
        ->groupBy(function($item) {
            return strtolower(trim($item->product_name));
        })
        ->map(function ($group) {
            $item = $group->first();
            return [
                'id'        => $item->product_id,
                'name'      => $item->product_name,
                'sku'       => null,
                'unit'      => $item->unit,
                'last_cost' => (float) $item->unit_cost,
            ];
        })
        ->values()
        ->take(10);

    // Debug: Log final results
    \Log::info('Returning ' . $results->count() . ' unique results for query: ' . $q);

    return response()->json($results);
}

}