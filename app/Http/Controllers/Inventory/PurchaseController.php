<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Stock;
use App\Models\StockBatch;
use App\Models\Tax;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Business;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->input('search', $request->input('q', '')));
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $businessId = $request->input('business_id');

        $purchases = Purchase::query()
            ->with(['supplier','creator','business'])
            ->when($q, function ($qq) use ($q) {
                $qq->where('invoice_no','like',"%{$q}%")
                   ->orWhereHas('supplier', fn($s) => $s->where('name','like',"%{$q}%"))
                   ->orWhereHas('business', fn($b) => $b->where('business_name','like',"%{$q}%"));
            })
            ->when($businessId, fn($qq) => $qq->where('business_id', $businessId))
            ->when($dateFrom, fn($qq) => $qq->whereDate('purchase_date', '>=', $dateFrom))
            ->when($dateTo, fn($qq) => $qq->whereDate('purchase_date', '<=', $dateTo))
            ->orderByDesc('purchase_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('inventory.purchases.index', compact('purchases', 'q', 'dateFrom', 'dateTo', 'businessId'))
            ->with('businesses', Business::orderBy('business_name')->get());
    }

    public function create()
{
    // Get products
        $products = Product::with(['stock', 'business'])
            ->orderBy('name')
            ->get();
    
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
            'business_id' => $product->business_id,
            'business_name' => $product->business->business_name ?? null,
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
            'business_id'    => ['required', 'exists:businesses,id'],
            'supplier_id'    => ['required', 'exists:suppliers,id'],
            'purchase_date'  => ['required', 'date'],
            'invoice_no'     => ['required', 'string', 'max:100'],
            'payment_method' => ['required', 'in:cash,credit,bank'],
            'final_tax_id'   => ['nullable', 'integer', 'exists:taxes,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'], // Can be null for new products
            'items.*.product_name' => ['required', 'string', 'max:255'], // Product name (existing or new)
            'items.*.product_unit' => ['required', 'in:kg,liter,pcs,cartoon,peti,bori,box,bottle,pack,set'], // Unit for new products
            'items.*.category_id' => ['nullable', 'exists:categories,id'], // Category for new products
            'items.*.category_name' => ['nullable', 'string', 'max:255'], // Category name for auto-creation
            'items.*.brand_id' => ['nullable', 'exists:brands,id'], // Brand for new products
            'items.*.brand_name' => ['nullable', 'string', 'max:255'], // Brand name for auto-creation
            'items.*.qty' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_cost' => ['required', 'integer', 'min:0', 'max:9999999'],
            'items.*.expiry_date' => ['nullable', 'date'],
        ]);

        DB::transaction(function () use ($data) {
            // Create purchase header
            $purchase = Purchase::create([
                'business_id'    => $data['business_id'],
                'supplier_id'    => $data['supplier_id'],
                'created_by'     => auth()->id(),
                'purchase_date'  => $data['purchase_date'],
                'invoice_no'     => $data['invoice_no'] ?? null,
                'payment_method' => $data['payment_method'],
                'total_cost'     => 0,
            ]);

            $purchaseBaseTotal = 0;

            foreach ($data['items'] as $row) {
                $qty = (float) $row['qty'];
                $unitCost = (float) $row['unit_cost'];
                $baseCost = $qty * $unitCost;
                $normalizedProductName = $this->normalizeProductName($row['product_name']);

                // Get category and brand names for snapshot
                $categoryName = null;
                $companyName = null;

                // Check if product exists, if not create it
                if (!empty($row['product_id'])) {
                    // Existing product - get category and brand from product
                    $productId = $row['product_id'];
                    $existingProduct = Product::with(['category', 'brandRelation'])->find($productId);
                    if ($existingProduct) {
                        $brandId = $this->resolveBrandId($row);

                        if ($brandId && (int) $existingProduct->brand_id !== (int) $brandId) {
                            $this->assertNoBusinessProductConflict(
                                $normalizedProductName,
                                $brandId,
                                (int) $data['business_id'],
                                $existingProduct->id
                            );
                        }

                        $categoryName = $existingProduct->category->name ?? null;
                        $companyName = $existingProduct->brandRelation->name ?? null;
                        
                        // Check if user wants to update brand for existing product
                        $updateBrand = false;
                        if ($brandId) {
                            $brand = Brand::find($brandId);
                            if ($brand) {
                                $existingProduct->brand_id = $brand->id;
                                $companyName = $brand->name;
                                $updateBrand = true;
                            }
                        }
                        
                        // Check if user wants to update category for existing product
                        $updateCategory = false;
                        if (!empty($row['category_id'])) {
                            $category = Category::find($row['category_id']);
                            if ($category) {
                                $existingProduct->category_id = $category->id;
                                $categoryName = $category->name;
                                $updateCategory = true;
                            }
                        } elseif (!empty($row['category_name']) && empty($existingProduct->category_id)) {
                            // Create new category from name if product has no category
                            $category = Category::firstOrCreate(
                                ['name' => trim($row['category_name'])],
                                ['name' => trim($row['category_name'])]
                            );
                            $existingProduct->category_id = $category->id;
                            $categoryName = $category->name;
                            $updateCategory = true;
                        }
                        
                        // Save product if brand or category was updated
                        if ($updateBrand || $updateCategory) {
                            $existingProduct->save();
                        }
                    }
                } else {
                    $businessId = $data['business_id'];
                    $brandId = $this->resolveBrandId($row);
                    $this->assertNoBusinessProductConflict(
                        $normalizedProductName,
                        $brandId,
                        (int) $businessId
                    );

                    // Handle category - use existing ID or create from name
                    $categoryId = null;
                    if (!empty($row['category_id'])) {
                        $category = Category::find($row['category_id']);
                        $categoryId = $category->id ?? null;
                        $categoryName = $category->name ?? null;
                    } elseif (!empty($row['category_name'])) {
                        // Create new category from name or find existing
                        $category = Category::firstOrCreate(
                            ['name' => trim($row['category_name'])],
                            ['name' => trim($row['category_name'])]
                        );
                        $categoryId = $category->id;
                        $categoryName = $category->name;
                    }
                    
                    // Handle brand - use existing ID or create from name
                    if ($brandId) {
                        $brand = Brand::find($brandId);
                        $companyName = $brand->name ?? null;
                    }

                    // Create new product with category and brand
                    $product = Product::create([
                        'business_id' => $businessId,
                        'name' => $normalizedProductName,
                        'unit' => $row['product_unit'],
                        'category_id' => $categoryId ?? 1, // Use selected or default category
                        'brand_id' => $brandId, // Use selected brand
                        'selling_price' => $unitCost * 1.2, // 20% markup by default
                        'is_active' => true,
                    ]);

                    // Ensure stock row exists for the new product without duplicate-key failures.
                    Stock::firstOrCreate(
                        ['product_id' => $product->id],
                        ['quantity' => 0, 'reorder_level' => 0]
                    );

                    $productId = $product->id;
                }

                // Create purchase item with category and company snapshots
                $purchaseItem = PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $productId,
                    'product_name' => $row['product_name'],         // snapshot
                    'category_name' => $categoryName,               // snapshot
                    'company_name' => $companyName,                 // snapshot
                    'unit' => $row['product_unit'],  
                    'qty' => $qty,
                    'unit_cost' => $unitCost,
                    'base_cost' => $baseCost,
                    'tax_total' => 0,
                    'line_total' => $baseCost,
                    'expiry_date' => $row['expiry_date'] ?? null,
                ]);

                $batchNo = StockBatch::generateBatchNo($data['purchase_date']);

                StockBatch::create([
                    'product_id' => $productId,
                    'purchase_item_id' => $purchaseItem->id,
                    'batch_no' => $batchNo,
                    'qty_received' => $qty,
                    'qty_remaining' => $qty,
                    'unit_cost' => $unitCost,
                    'expiry_date' => $row['expiry_date'] ?? null,
                    'purchased_on' => $data['purchase_date'],
                    'status' => 'active',
                ]);

                $purchaseItem->update(['batch_no' => $batchNo]);

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
            $purchaseTotal = (int) round($purchaseBaseTotal + $finalTaxAmount);
            $purchase->update(['total_cost' => $purchaseTotal]);

            // Money movements based on payment method
            if (in_array($data['payment_method'], ['cash', 'bank'])) {
                // Paid immediately — deduct from business balance
                $business = \App\Models\Business::find($data['business_id']);
                if ($business) {
                    $business->decrement('balance', $purchaseTotal);
                }
            }

            // Always sync supplier due (credit purchases increase it; cash/bank do not
            // because calculateTotalDue() now only sums credit purchases)
            $supplier = Supplier::find($data['supplier_id']);
            if ($supplier) {
                $supplier->syncTotalDue();
            }
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

        $expiringSoon = StockBatch::query()
            ->with(['product.business', 'product.brandRelation', 'purchaseItem.purchase.supplier'])
            ->where('status', 'active')
            ->where('qty_remaining', '>', 0)
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$today, $soon])
            ->orderBy('expiry_date')
            ->paginate(10, ['*'], 'soon_page')
            ->withQueryString();

        $expired = StockBatch::query()
            ->with(['product.business', 'product.brandRelation', 'purchaseItem.purchase.supplier'])
            ->where('qty_remaining', '>', 0)
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
    $businessId = $request->get('business_id');
    $showAll = $request->boolean('show_all');

    if (strlen($q) < 2) {
        return response()->json([]);
    }

    // Search real products so we don't hide items that were never purchased.
    $products = Product::query()
        ->with(['stock', 'ecommerceProduct', 'latestPurchaseItem'])
        ->when($businessId, fn($query) => $query->where('business_id', $businessId))
        ->where('name', 'LIKE', '%' . $q . '%')
        ->when(!$showAll, function ($query) {
            $query->whereHas('stock', function ($stockQuery) {
                $stockQuery->where('quantity', '>', 0);
            });
        })
        ->orderBy('name')
        ->limit(10)
        ->get();

    $results = $products->map(function (Product $product) {
        $lastPurchaseCost = (float) ($product->latestPurchaseItem?->unit_cost ?? 0);
        $availableStock = (float) ($product->stock?->quantity ?? 0);

        return [
            'id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'unit' => $product->unit,
            'selling_price' => (float) ($product->selling_price ?? 0),
            'last_purchase_cost' => $lastPurchaseCost,
            // Backward-compatible alias for existing UI bindings.
            'last_cost' => $lastPurchaseCost,
            'available_stock' => $availableStock,
            'pos_available' => $product->posAvailableStock(),
            'business_id' => $product->business_id,
        ];
    });

    return response()->json($results);
}
public function export($type, Request $request)
{
    // Get date filters
    $from = $request->get('from');
    $to = $request->get('to');
    $businessId = $request->get('business_id');
    $businessName = $businessId ? Business::find($businessId)?->business_name : null;
    
    // Debug: Log the received parameters
    \Log::info('Export parameters:', [
        'type' => $type,
        'from' => $from,
        'to' => $to
    ]);
    
    // Build query with date filtering
    $query = Purchase::with(['supplier', 'creator', 'business']);
    if ($businessId) {
        $query->where('business_id', $businessId);
    }
    
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
            fputcsv($file, ['Date', 'Business', 'Supplier', 'Invoice', 'Total', 'Created By']);
            
            foreach ($purchases as $p) {
                fputcsv($file, [
                    $p->purchase_date->format('Y-m-d'),
                    $p->business->business_name ?? '',
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
            'to' => $to,
            'businessId' => $businessId,
            'businessName' => $businessName,
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
            'to' => $to,
            'businessId' => $businessId,
            'businessName' => $businessName,
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

/**
 * Create a new category via AJAX (for purchase form)
 */
public function storeCategory(Request $request)
{
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
    ]);

    $category = Category::create(['name' => $data['name']]);

    return response()->json([
        'success' => true,
        'category' => [
            'id' => $category->id,
            'name' => $category->name,
        ]
    ]);
}

/**
 * Search categories via AJAX (for purchase form autocomplete)
 */
public function searchCategories(Request $request)
{
    $q = trim($request->get('q', ''));
    if (strlen($q) < 1) return response()->json([]);

    $categories = Category::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($q) . '%'])
        ->orderBy('name')
        ->limit(10)
        ->get()
        ->map(function($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
            ];
        });

    return response()->json($categories);
}

/**
 * Create a new brand/company via AJAX (for purchase form)
 */
public function storeBrand(Request $request)
{
    $data = $request->validate([
        'name' => ['required', 'string', 'max:255', 'unique:brands,name'],
    ]);

    $brand = Brand::create(['name' => $data['name']]);

    return response()->json([
        'success' => true,
        'brand' => [
            'id' => $brand->id,
            'name' => $brand->name,
        ]
    ]);
}

/**
 * Search brands/companies via AJAX (for purchase form autocomplete)
 */
public function searchBrands(Request $request)
{
    $q = trim($request->get('q', ''));
    if (strlen($q) < 1) return response()->json([]);

    $brands = Brand::whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($q) . '%'])
        ->orderBy('name')
        ->limit(10)
        ->get()
        ->map(function($brand) {
            return [
                'id' => $brand->id,
                'name' => $brand->name,
            ];
        });

    return response()->json($brands);
}

private function normalizeProductName(string $name): string
{
    return trim(preg_replace('/\s+/', ' ', $name));
}

private function resolveBrandId(array $row): ?int
{
    if (!empty($row['brand_id'])) {
        return (int) $row['brand_id'];
    }

    if (!empty($row['brand_name'])) {
        $brandName = trim($row['brand_name']);
        if ($brandName === '') {
            return null;
        }

        $brand = Brand::firstOrCreate(
            ['name' => $brandName],
            ['name' => $brandName]
        );

        return $brand->id;
    }

    return null;
}

private function assertNoBusinessProductConflict(
    string $normalizedProductName,
    ?int $brandId,
    int $businessId,
    ?int $ignoreProductId = null
): void {
    $query = Product::query()
        ->with('business')
        ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($normalizedProductName)])
        ->when($ignoreProductId, fn($q) => $q->where('id', '!=', $ignoreProductId));

    if (is_null($brandId)) {
        $query->whereNull('brand_id');
    } else {
        $query->where('brand_id', $brandId);
    }

    $conflict = $query->where('business_id', '!=', $businessId)->first();

    if ($conflict) {
        $owner = $conflict->business->business_name ?? 'another business';
        throw ValidationException::withMessages([
            'brand_name' => "This product with the same company already belongs to {$owner}. Choose a different company for this business entry.",
        ]);
    }

    $sameBusinessConflict = Product::query()
        ->where('business_id', $businessId)
        ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($normalizedProductName)])
        ->when($ignoreProductId, fn($q) => $q->where('id', '!=', $ignoreProductId));

    if (is_null($brandId)) {
        $sameBusinessConflict->whereNull('brand_id');
    } else {
        $sameBusinessConflict->where('brand_id', $brandId);
    }

    if ($sameBusinessConflict->exists()) {
        throw ValidationException::withMessages([
            'brand_name' => 'This business already has the same product under the same company.',
        ]);
    }
}

}
