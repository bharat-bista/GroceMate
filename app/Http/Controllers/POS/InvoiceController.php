<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Mail\InvoiceMail;
use App\Models\POS\Customer;
use App\Models\Product;
use App\Models\POS\Income;
use App\Models\POS\Invoice;
use App\Models\POS\InvoiceItem;
use App\Models\Stock;
use App\Models\StockBatch;
use App\Models\Tax;
use App\Services\FifoStockService;
use App\Services\InvoiceNumberService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function __construct(private FifoStockService $fifoService)
    {
    }

    public function index(Request $request)
    {
        // Start with base query
        $query = Invoice::select('invoices.*', 'customers.name as customer_name', 'customers.total_due as customer_total_due')
                   ->join('customers', 'invoices.customer_id', '=', 'customers.id')
                   ->with(['creator', 'business']);

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            \Log::info('Search term: ' . $search);
            
            $query->where('invoices.invoice_no', 'like', '%'.$search.'%')
                   ->orWhere('customers.name', 'like', '%'.$search.'%');
        }

        // Date filtering
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('invoices.invoice_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('invoices.invoice_date', '<=', $request->date_to);
        }

        $invoices = $query->orderByDesc('invoices.id')->paginate(10);

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
            'invoice_date' => ['required', 'date'],
            'invoice_no' => ['nullable', 'string', 'max:100'],
            'payment_method' => ['required', 'in:cash,credit,bank'],
            'discount_pct'   => ['nullable', 'numeric', 'min:0', 'max:100'],
            'final_tax_id'   => ['nullable', 'integer', 'exists:taxes,id'],
            'send_email' => ['nullable', 'boolean'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id'   => ['nullable', 'exists:products,id'],
            'items.*.batch_id'     => ['nullable', 'integer', 'exists:stock_batches,id'],
            'items.*.product_name' => ['required', 'string', 'max:255'],
            'items.*.product_unit' => ['required', 'in:kg,liter,pcs,cartoon,peti,bori,box,bottle,pack,set'],
            'items.*.qty'          => ['required', 'numeric', 'gt:0'],
            'items.*.unit_cost'    => ['required', 'integer', 'min:0', 'max:9999999'],
        ]);

        $sendEmail = $data['send_email'] ?? false;
        $invoiceId = null;

        // Validate stock for every row before any DB changes, so we can show per-row errors.
        $errors = [];
        $fifoService = $this->fifoService ?? app(FifoStockService::class);
        foreach ($data['items'] as $index => $row) {
            $qty   = (float) $row['qty'];
            $label = $row['product_name'];

            if (!empty($row['batch_id'])) {
                // Batch-specific: check the chosen batch has enough POS-available stock
                // (raw qty minus ecommerce reservation distributed FIFO from oldest batches).
                $batch = StockBatch::find((int) $row['batch_id']);
                $posAvailable = $batch ? $fifoService->batchPosAvailable((int) $row['batch_id']) : 0.0;
                if (!$batch || $batch->status !== 'active' || $posAvailable < $qty) {
                    $errors["items.{$index}.qty"] = "{$label} — batch only has {$posAvailable} available for POS, you need {$qty}";
                }
            } else {
                $check = $fifoService->canConsume((int) ($row['product_id'] ?? 0), $qty, 'pos');
                if (!$check['ok']) {
                    $errors["items.{$index}.qty"] = "{$label} — only {$check['available']} available, you need {$qty}";
                }
            }
        }

        if (!empty($errors)) {
            throw ValidationException::withMessages($errors);
        }

        DB::transaction(function () use ($data, &$invoiceId, $fifoService) {
            // Create invoice header
            $invoice = Invoice::create([
                'business_id' => $data['business_id'],
                'customer_id' => $data['customer_id'],
                'created_by' => auth()->id(),
                'invoice_date' => $data['invoice_date'],
                'invoice_no' => InvoiceNumberService::generateInvoiceNumber(),
                'total_cost' => 0,
                'discount'   => 0,
                'payment_method' => $data['payment_method'],
            ]);

            $invoiceId = $invoice->id;

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
                        'business_id' => $data['business_id'],
                        'name' => $row['product_name'],
                        'unit' => $row['product_unit'],
                        'category_id' => config('pos.default_category_id', 1),
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
                $invoiceItem = InvoiceItem::create([
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

                // Consume from the user-selected batch, or fall back to FIFO order.
                if (!empty($row['batch_id'])) {
                    $result = $fifoService->consumeFromBatch((int) $row['batch_id'], $qty);
                } else {
                    $result = $fifoService->consume($productId, $qty, 'pos');
                }
                $invoiceItem->update([
                    'batches_consumed' => json_encode($result['batches_used']),
                ]);
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

            // Apply percentage discount on (base + tax), then store both
            $discountPct    = (float) ($data['discount_pct'] ?? 0);
            $discountAmount = (int) round(($purchaseBaseTotal + $finalTaxAmount) * $discountPct / 100);
            $invoiceTotal   = max(0, (int) round($purchaseBaseTotal + $finalTaxAmount - $discountAmount));
            $invoice->update(['total_cost' => $invoiceTotal, 'discount' => $discountAmount]);

            // Cash/bank sales are collected immediately — record income so the
            // business account balance is updated via the Income model's created event.
            // Credit sales only add to customer due; no money received yet.
            if (in_array($data['payment_method'], ['cash', 'bank']) && $invoiceTotal > 0) {
                Income::create([
                    'reference_no'     => $invoice->invoice_no,
                    'customer_id'      => $data['customer_id'],
                    'business_id'      => $data['business_id'],
                    'created_by'       => auth()->id(),
                    'transaction_date' => $data['invoice_date'],
                    'amount_received'  => $invoiceTotal,
                    'payment_method'   => $data['payment_method'],
                    'income_type'      => 'Sale',
                    'description'      => 'POS Sale — Invoice ' . $invoice->invoice_no,
                ]);
            }

            $customer = Customer::find($data['customer_id']);
            if ($customer) {
                $customer->syncTotalDue();
            }
        });

        // Send email if requested
        if ($sendEmail && $invoiceId) {
            $this->sendInvoiceEmail($invoiceId);
        }

        $message = $sendEmail ? 'Invoice saved successfully and email sent to customer.' : 'Invoice saved successfully.';
        return redirect()->route('pos.invoices.index')
            ->with('success', $message);
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
            $query->whereBetween('invoice_date', [$from, $to]);
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
                        $invoice->invoice_date->format('Y-m-d'),
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
            $query->whereBetween('invoice_date', [$from, $to]);
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
                    $invoice->invoice_date->format('Y-m-d'),
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

    private function sendInvoiceEmail($invoiceId)
    {
        try {
            $invoice = Invoice::with(['customer', 'creator', 'items.product', 'business'])->find($invoiceId);
            
            if (!$invoice || !$invoice->customer->email) {
                return false;
            }

            // Generate PDF
            $filename = 'invoice_' . $invoice->id . '_' . now()->format('Y-m-d') . '.pdf';
            $html = view('pos.invoices.export-individual-pdf', [
                'invoice' => $invoice
            ])->render();
            
            $pdf = new \Dompdf\Dompdf();
            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();
            $pdfContent = $pdf->output();

            // Send email using mailable class
            Mail::to($invoice->customer->email)->send(new InvoiceMail($invoice, $pdfContent, $filename));

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send invoice email: ' . $e->getMessage());
            return false;
        }
    }

    public function cancel(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
        ]);

        if ($invoice->cancellation_status === 'cancelled') {
            return back()->with('error', 'Invoice already cancelled');
        }

        $reason = $data['reason'];
        $fifoService = $this->fifoService ?? app(FifoStockService::class);

        DB::transaction(function () use ($invoice, $reason, $fifoService) {
            $invoice->loadMissing('items');

            // Restore stock back to the exact FIFO batches used on this invoice.
            foreach ($invoice->items as $item) {
                $batchesUsed = [];
                if (!empty($item->batches_consumed)) {
                    $decoded = json_decode($item->batches_consumed, true);
                    $batchesUsed = is_array($decoded) ? $decoded : [];
                }

                $fifoService->reverse($item->product_id, (float) $item->qty, $batchesUsed);
            }

            // Mark the invoice as cancelled and capture audit details.
            $invoice->update([
                'cancellation_status' => 'cancelled',
                'cancelled_at' => now(),
                'cancelled_by' => auth()->id(),
                'cancellation_reason' => $reason,
            ]);

            $customer = Customer::find($invoice->customer_id);
            if ($customer) {
                $customer->syncTotalDue();
            }
        });

        return redirect()->route('pos.invoices.index')
            ->with('success', 'Invoice cancelled successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $fifoService = $this->fifoService ?? app(FifoStockService::class);

        DB::transaction(function () use ($invoice, $fifoService) {
            $invoice->loadMissing('items');

            // Restore stock via the same FIFO reverse path used by cancel(),
            // so both qty_remaining and status on stock_batches stay correct.
            foreach ($invoice->items as $item) {
                $batchesUsed = [];
                if (!empty($item->batches_consumed)) {
                    $decoded = json_decode($item->batches_consumed, true);
                    $batchesUsed = is_array($decoded) ? $decoded : [];
                }

                $fifoService->reverse($item->product_id, (float) $item->qty, $batchesUsed);
            }

            $invoice->delete();

            $customer = Customer::find($invoice->customer_id);
            if ($customer) {
                $customer->syncTotalDue();
            }
        });

        return redirect()->route('pos.invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }

    public function sendEmail(Invoice $invoice)
    {
        try {
            // Check if customer has email
            if (!$invoice->customer || !$invoice->customer->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer does not have an email address.'
                ], 400);
            }

            // Send email
            $result = $this->sendInvoiceEmail($invoice->id);
            
            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Invoice sent successfully to ' . $invoice->customer->email
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send invoice. Please check your email configuration.'
                ], 500);
            }
        } catch (\Exception $e) {
            \Log::error('Error sending invoice email: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the invoice.'
            ], 500);
        }
    }

}
