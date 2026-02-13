<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\POS\Customer;
use App\Models\Product;
use App\Models\POS\Invoice;
use App\Models\POS\InvoiceItem;
use App\Models\Stock;
use App\Models\Tax;
use App\Services\InvoiceNumberService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['customer', 'creator', 'business'])
            ->orderByDesc('id')
            ->paginate(10);

        return view('pos.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'creator', 'items.product', 'business']);
        return view('pos.invoices.show', compact('invoice'));
    }

    public function create()
    {
        // Generate next invoice number
        $nextInvoiceNumber = InvoiceNumberService::generateInvoiceNumber();
        
        return view('pos.invoices.create', [
            'businesses' => \App\Models\Business::orderBy('business_name')->get(),
            'customers' => Customer::orderBy('name')->get(),
            'products' => Product::with('stock')->orderBy('name')->get(),
            'taxes' => Tax::orderBy('name')->get(),
            'nextInvoiceNumber' => $nextInvoiceNumber,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_id' => ['required', 'exists:businesses,id'],
            'customer_id' => ['required', 'exists:customers,id'],
            'purchase_date' => ['required', 'date'],
            'invoice_no' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['required', 'in:cash,credit,bank'],
            'final_tax_id' => ['nullable', 'integer', 'exists:taxes,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['nullable', 'exists:products,id'], // Can be null for new products
            'items.*.product_name' => ['required', 'string', 'max:255'], // Product name (existing or new)
            'items.*.product_unit' => ['required', 'in:kg,liter,pcs,cartoon,peti,bori,box,bottle,pack,set'], // Unit for new products
            'items.*.qty' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
        ]);

        DB::transaction(function () use ($data) {
            // Create invoice header
            $invoice = Invoice::create([
                'business_id' => $data['business_id'],
                'customer_id' => $data['customer_id'],
                'created_by' => auth()->id(),
                'purchase_date' => $data['purchase_date'],
                'invoice_no' => InvoiceNumberService::generateInvoiceNumber(),
                'total_cost' => 0,
                'payment_method' => $data['payment_method'],
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
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $productId,
                    'product_name' => $row['product_name'],         // snapshot
                    'unit' => $row['product_unit'],  
                    'qty' => $qty,
                    'unit_cost' => $unitCost,
                    'base_cost' => $baseCost,
                    'tax_total' => 0,
                    'line_total' => $baseCost,
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

    public function export(Invoice $invoice, Request $request, $format)
    {
        \Log::info('Individual export called with format: ' . $format . ' for invoice: ' . $invoice->id);
        
        switch ($format) {
            case 'pdf':
                return $this->exportPDF($invoice);
            case 'csv':
                return $this->exportCSV($invoice);
            case 'excel':
                return $this->exportExcel($invoice);
            default:
                \Log::error('Invalid export format: ' . $format);
                return redirect()->back()->with('error', 'Invalid export format');
        }
    }

    public function bulkExport(Request $request, $format)
    {
        \Log::info('bulkExport called with format: ' . $format);
        
        switch ($format) {
            case 'pdf':
                return $this->bulkExportPDF();
            case 'csv':
                return $this->bulkExportCSV();
            case 'excel':
                return $this->bulkExportExcel();
            default:
                \Log::error('Invalid export format: ' . $format);
                return redirect()->back()->with('error', 'Invalid export format');
        }
    }

    private function bulkExportPDF()
    {
        // Get date filters
        $from = request()->get('from');
        $to = request()->get('to');
        
        // Build query with date filters if provided
        $query = Invoice::with(['customer', 'creator', 'items.product', 'business']);
        
        if ($from && $to) {
            $query->whereBetween('purchase_date', [$from, $to]);
        }
        
        $invoices = $query->get();
        
        $filename = 'invoices_' . now()->format('Y-m-d') . '.pdf';
        
        $html = view('pos.invoices.export-pdf', [
            'invoices' => $invoices,
            'from' => $from,
            'to' => $to
        ])->render();
        
        // Use simple PDF generation with DOMPDF style like PurchaseController
        $pdf = new \Dompdf\Dompdf();
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();
            
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    private function bulkExportCSV()
    {
        $invoices = Invoice::with(['customer', 'creator', 'items.product', 'business'])->get();
        
        $filename = 'all-invoices.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($invoices) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'Invoice No',
                'Date',
                'Business',
                'Customer',
                'Product',
                'Quantity',
                'Unit',
                'Unit Price',
                'Total',
                'Payment Method'
            ]);
            
            // Data rows
            foreach ($invoices as $invoice) {
                foreach ($invoice->items as $item) {
                    fputcsv($file, [
                        $invoice->invoice_no,
                        $invoice->purchase_date->format('Y-m-d'),
                        $invoice->business->business_name ?? 'N/A',
                        $invoice->customer->name ?? 'N/A',
                        $item->product_name,
                        $item->qty,
                        $item->unit,
                        $item->unit_cost,
                        $item->line_total,
                        $invoice->payment_method
                    ]);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function bulkExportExcel()
    {
        // Get date filters
        $from = request()->get('from');
        $to = request()->get('to');
        
        // Build query with date filters if provided
        $query = Invoice::with(['customer', 'creator', 'items.product', 'business']);
        
        if ($from && $to) {
            $query->whereBetween('purchase_date', [$from, $to]);
        }
        
        $invoices = $query->get();
        
        $filename = 'invoices_' . now()->format('Y-m-d') . '.xlsx';
        
        // Generate HTML table that Excel can open
        $html = view('pos.invoices.export-excel', [
            'invoices' => $invoices,
            'from' => $from,
            'to' => $to
        ])->render();
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        return response($html, 200, $headers);
    }

    private function exportPDF(Invoice $invoice)
    {
        $invoice->load(['customer', 'creator', 'items.product', 'business']);
        
        $filename = 'invoice_' . $invoice->id . '_' . now()->format('Y-m-d') . '.pdf';
        
        $html = view('pos.invoices.export-individual-pdf', [
            'invoice' => $invoice
        ])->render();
        
        // Use simple PDF generation with DOMPDF style like PurchaseController
        $pdf = new \Dompdf\Dompdf();
        $pdf->loadHtml($html);
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
            
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }

    private function exportCSV(Invoice $invoice, $isExcel = false)
    {
        $invoice->load(['customer', 'creator', 'items.product', 'business']);
        
        $filename = 'invoice-' . $invoice->invoice_no . ($isExcel ? '.xlsx' : '.csv');
        
        $headers = [
            'Content-Type' => $isExcel ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($invoice) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'Invoice No',
                'Date',
                'Business',
                'Customer',
                'Product',
                'Quantity',
                'Unit',
                'Unit Price',
                'Total',
                'Payment Method'
            ]);
            
            // Data rows
            foreach ($invoice->items as $item) {
                fputcsv($file, [
                    $invoice->invoice_no,
                    $invoice->purchase_date->format('Y-m-d'),
                    $invoice->business->business_name ?? 'N/A',
                    $invoice->customer->name ?? 'N/A',
                    $item->product_name,
                    $item->qty,
                    $item->unit,
                    $item->unit_cost,
                    $item->line_total,
                    $invoice->payment_method
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportExcel(Invoice $invoice)
    {
        $invoice->load(['customer', 'creator', 'items.product', 'business']);
        
        $filename = 'invoice_' . $invoice->id . '_' . now()->format('Y-m-d') . '.xlsx';
        
        // Generate HTML table that Excel can open
        $html = view('pos.invoices.export-individual-excel', [
            'invoice' => $invoice
        ])->render();
        
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        return response($html, 200, $headers);
    }

}
