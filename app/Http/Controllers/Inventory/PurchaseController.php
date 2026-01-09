<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Stock;
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
        return view('inventory.purchases.create', [
            'suppliers' => Supplier::orderBy('name')->get(),
            'products'  => Product::with('stock')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => ['required','exists:suppliers,id'],
            'purchase_date' => ['required','date'],
            'invoice_no' => ['nullable','string','max:100'],

            'items' => ['required','array','min:1'],
            'items.*.product_id' => ['required','exists:products,id'],
            'items.*.qty' => ['required','numeric','gt:0'],
            'items.*.unit_cost' => ['required','numeric','min:0'],
            'items.*.expiry_date' => ['nullable','date'],
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

            $total = 0;

            foreach ($data['items'] as $row) {
                $qty = (float)$row['qty'];
                $unitCost = (float)$row['unit_cost'];
                $lineTotal = $qty * $unitCost;
                $total += $lineTotal;

                // Save purchase item (batch w/ expiry)
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $row['product_id'],
                    'qty' => $qty,
                    'unit_cost' => $unitCost,
                    'expiry_date' => $row['expiry_date'] ?? null,
                    'line_total' => $lineTotal,
                ]);

                // Update stock (increase)
                $stock = Stock::firstOrCreate(
    ['product_id' => $row['product_id']],
    ['quantity' => 0, 'reorder_level' => 0]
);

$stock->increment('quantity', $qty);

            }

            $purchase->update(['total_cost' => $total]);
        });

        return redirect()->route('inventory.purchases.index')
            ->with('success', 'Purchase saved and stock updated.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier','creator','items.product']);
        return view('inventory.purchases.show', compact('purchase'));
    }

    /**
     * Expiry Alerts page:
     * - Expiring Soon: expiry_date within N days
     * - Expired: expiry_date < today
     */
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
}
