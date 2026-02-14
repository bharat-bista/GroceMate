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
            ->with(['supplier','creator','business'])
            ->when($q, function ($qq) use ($q) {
                $qq->where('invoice_no','like',"%{$q}%")
                   ->orWhereHas('supplier', fn($s) => $s->where('name','like',"%{$q}%"))
                   ->orWhereHas('business', fn($b) => $b->where('business_name','like',"%{$q}%"));
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
        'businesses' => \App\Models\Business::orderBy('business_name')->get(),
        'suppliers' => Supplier::orderBy('name')->get(),
        'products'  => $products,
        'taxes'     => Tax::orderBy('name')->get(),
    ]);
}

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_id' => ['required', 'exists:businesses,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'purchase_date' => ['required', 'date'],
            'invoice_no' => ['required', 'string', 'max:100'],
            'final_tax_id' => ['nullable', 'integer', 'exists:taxes,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'], // Can be null for new products
            'items.*.product_name' => ['required', 'string', 'max:255'], // Product name (existing or new)
            'items.*.product_unit' => ['required', 'in:kg,liter,pcs,cartoon,peti,bori,box,bottle,pack,set'], // Unit for new products
            'items.*.qty' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'items.*.expiry_date' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($data) {
            // Create purchase header
            $purchase = Purchase::create([
                'business_id' => $data['business_id'],
                'supplier_id' => $data['supplier_id'],
                'created_by' => auth()->id(),
                'purchase_date' => $data['purchase_date'],
                'invoice_no' => $data['invoice_no'] ?? null,
                'total_cost' => 0,
            ]);

            $purchaseBaseTotal = 0;

            foreach ($data['items'] as $row) {
                $qty = (float) $row['qty'];
                $unitCost = (float) $row['unit_cost'];
                $baseCost = $qty * $unitCost;

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

                // Create purchase item (no per-product taxes)
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $productId,
                    'product_name' => $row['product_name'],         // snapshot
                    'unit' => $row['product_unit'],  
                    'qty' => $qty,
                    'unit_cost' => $unitCost,
                    'base_cost' => $baseCost,
                    'tax_total' => 0,
                    'line_total' => $baseCost,
                    'expiry_date' => $row['expiry_date'] ?? null,
                ]);

                // Add to purchase base total
                $purchaseBaseTotal += $baseCost;

                // Update stock
                $stock = Stock::firstOrCreate(
                    ['product_id' => $productId],
                    ['quantity' => 0, 'reorder_level' => 0]
                );
                $stock->increment('quantity', $qty);
            }

            // Calculate final tax if selected
            $finalTaxAmount = 0;
            if (!empty($data['final_tax_id'])) {
                $tax = Tax::find($data['final_tax_id']);
                if ($tax) {
                    if ($tax->type === 'fixed') {
                        $finalTaxAmount = $tax->rate;
                    } else {
                        $finalTaxAmount = ($purchaseBaseTotal * $tax->rate) / 100;
                    }
                }
            }

            // Update purchase total with final tax
            $purchaseTotal = $purchaseBaseTotal + $finalTaxAmount;
            $purchase->update(['total_cost' => $purchaseTotal]);
        });

        return redirect()->route('inventory.purchases.index')
            ->with('success', 'Purchase saved and stock updated.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'creator', 'items.product', 'business']);
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
public function export($type, Request $request)
{
    // Get date filters
    $from = $request->get('from');
    $to = $request->get('to');
    
    // Debug: Log the received parameters
    \Log::info('Export parameters:', [
        'type' => $type,
        'from' => $from,
        'to' => $to
    ]);
    
    // Build query with date filtering
    $query = Purchase::with(['supplier', 'creator']);
    
    if ($from && $to) {
        try {
            $fromDate = \Carbon\Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
            $toDate = \Carbon\Carbon::createFromFormat('Y-m-d', $to)->endOfDay();
            
            // Debug: Log the parsed dates
            \Log::info('Date filtering applied:', [
                'from_date' => $fromDate->toDateTimeString(),
                'to_date' => $toDate->toDateTimeString()
            ]);
            
            $query->whereBetween('purchase_date', [$fromDate, $toDate]);
        } catch (\Exception $e) {
            // Debug: Log the error
            \Log::error('Date parsing failed:', [
                'error' => $e->getMessage(),
                'from' => $from,
                'to' => $to
            ]);
        }
    }
    
    $purchases = $query->orderBy('purchase_date', 'desc')->get();
    
    // Debug: Log the results
    \Log::info('Total purchases found: ' . $purchases->count());
    
    if ($type === 'csv') {
        $filename = 'purchases_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function () use ($purchases) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Supplier', 'Invoice', 'Total', 'Created By']);
            
            foreach ($purchases as $p) {
                fputcsv($file, [
                    $p->purchase_date->format('Y-m-d'),
                    $p->supplier->name ?? '',
                    $p->invoice_no ?? '',
                    number_format($p->total_cost, 2),
                    $p->creator->name ?? '',
                ]);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    if ($type === 'pdf') {
        $filename = 'purchases_' . now()->format('Y-m-d') . '.pdf';
        
        $html = view('inventory.purchases.export-pdf', [
            'purchases' => $purchases,
            'from' => $from,
            'to' => $to
        ])->render();
        
        // Use simple PDF generation with DOMPDF style
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
        $filename = 'purchases_' . now()->format('Y-m-d') . '.xlsx';
        
        // Generate HTML table that Excel can open
        $html = view('inventory.purchases.export-excel', [
            'purchases' => $purchases,
            'from' => $from,
            'to' => $to
        ])->render();
        
        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
    
    abort(404);
}

// Test method for debugging date filtering
public function testDateFilter(Request $request)
{
    $from = $request->get('from');
    $to = $request->get('to');
    
    $query = Purchase::with(['supplier', 'creator']);
    
    if ($from && $to) {
        try {
            $fromDate = \Carbon\Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
            $toDate = \Carbon\Carbon::createFromFormat('Y-m-d', $to)->endOfDay();
            $query->whereBetween('purchase_date', [$fromDate, $toDate]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
    
    $purchases = $query->orderBy('purchase_date', 'desc')->get();
    
    return response()->json([
        'from' => $from,
        'to' => $to,
        'total_count' => $purchases->count(),
        'purchases' => $purchases->take(5)->map(function($p) {
            return [
                'id' => $p->id,
                'purchase_date' => $p->purchase_date->format('Y-m-d'),
                'supplier' => $p->supplier->name ?? 'N/A',
                'total' => $p->total_cost
            ];
        })
    ]);
}

// Individual purchase export method
public function exportIndividual(Purchase $purchase, $type)
{
    // Debug: Log the purchase ID to verify correct purchase is being exported
    \Log::info('Exporting purchase ID: ' . $purchase->id . ' of type: ' . $type);
    
    // Load purchase with relationships
    $purchase->load(['supplier', 'creator', 'items.product']);
    
    if ($type === 'csv') {
        $filename = 'purchase_' . $purchase->id . '_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function () use ($purchase) {
            $file = fopen('php://output', 'w');
            
            // Header info
            fputcsv($file, ['Purchase Details']);
            fputcsv($file, ['Purchase ID', $purchase->id]);
            fputcsv($file, ['Date', $purchase->purchase_date->format('Y-m-d')]);
            fputcsv($file, ['Supplier', $purchase->supplier->name ?? 'N/A']);
            fputcsv($file, ['Invoice No', $purchase->invoice_no ?? 'N/A']);
            fputcsv($file, ['Created By', $purchase->creator->name ?? 'N/A']);
            fputcsv($file, ['Total Cost', number_format($purchase->total_cost, 2)]);
            fputcsv($file, []); // Empty row
            
            // Items header
            fputcsv($file, ['Items']);
            fputcsv($file, ['Product', 'Quantity', 'Unit Cost', 'Line Total', 'Expiry Date']);
            
            foreach ($purchase->items as $item) {
                fputcsv($file, [
                    $item->product_name,
                    $item->qty,
                    number_format($item->unit_cost, 2),
                    number_format($item->line_total, 2),
                    $item->expiry_date?->format('Y-m-d') ?? 'N/A'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    if ($type === 'pdf') {
        $filename = 'purchase_' . $purchase->id . '_' . now()->format('Y-m-d') . '.pdf';
        
        $html = view('inventory.purchases.export-individual-pdf', [
            'purchase' => $purchase
        ])->render();
        
        $pdf = new \Dompdf\Dompdf();
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
    
    if ($type === 'excel') {
        $filename = 'purchase_' . $purchase->id . '_' . now()->format('Y-m-d') . '.xlsx';
        
        $html = view('inventory.purchases.export-individual-excel', [
            'purchase' => $purchase
        ])->render();
        
        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
    
    abort(404);
}

}