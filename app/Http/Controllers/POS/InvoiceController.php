<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\POS\Invoice;
use App\Models\Stock;
use App\Models\Tax;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function create()
    {
        return view('pos.invoice.create', [
            'customers' => Customer::orderBy('name')->get(),
            'products' => Product::with('stock')->orderBy('name')->get(),
            'taxes' => Tax::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
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
            // Create invoice header
            $invoice = Invoice::create([
                'customer_id' => $data['customer_id'],
                'created_by' => auth()->id(),
                'purchase_date' => $data['purchase_date'],
                'invoice_no' => $data['invoice_no'],
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

                // Create invoice item (no per-product taxes)
                \App\Models\POS\InvoiceItem::create([
                    'invoice_id' => $invoice->id,
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

                // Update stock (decrease for POS sales)
                $stock = Stock::firstOrCreate(
                    ['product_id' => $productId],
                    ['quantity' => 0, 'reorder_level' => 0]
                );
                $stock->decrement('quantity', $qty);
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

            // Update invoice total with final tax
            $invoiceTotal = $purchaseBaseTotal + $finalTaxAmount;
            $invoice->update(['total_cost' => $invoiceTotal]);
        });

        return redirect()->route('pos.invoices.index')
            ->with('success', 'Invoice saved successfully.');
    }

    public function index()
    {
        $invoices = Invoice::with(['customer', 'creator'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('pos.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'creator', 'items.product']);
        return view('pos.invoices.show', compact('invoice'));
    }
}
