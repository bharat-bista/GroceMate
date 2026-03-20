<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\SupplierPayment;
use App\Models\Supplier;
use App\Models\Business;
use App\Models\POS\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class SupplierPaymentController extends Controller
{
    public function index(Request $request)
    {
        // ── Filters ──────────────────────────────────────────────────────────
        $q             = $request->input('q');
        $dateFrom      = $request->input('date_from');
        $dateTo        = $request->input('date_to');
        $paymentMethod = $request->input('payment_method');

        // ── Paginated payments (with search/filter) ───────────────────────────
        $payments = SupplierPayment::with('supplier')
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->whereHas('supplier', fn($sq) => $sq->where('name', 'like', "%{$q}%"))
                        ->orWhere('payment_reference', 'like', "%{$q}%");
                });
            })
            ->when($dateFrom, fn($query) => $query->whereDate('date', '>=', $dateFrom))
            ->when($dateTo,   fn($query) => $query->whereDate('date', '<=', $dateTo))
            ->when($paymentMethod, fn($query) => $query->where('payment_method', $paymentMethod))
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        // ── Summary cards ─────────────────────────────────────────────────────
        $totalPaid           = SupplierPayment::sum('amount');
        $thisMonthPaid       = SupplierPayment::whereYear('date', now()->year)
                                              ->whereMonth('date', now()->month)
                                              ->sum('amount');
        $totalOutstandingDue = Supplier::sum('total_due');

        // ── Top 5 suppliers by total paid ─────────────────────────────────────
        $topSuppliers = SupplierPayment::join('suppliers', 'supplier_payments.supplier_id', '=', 'suppliers.id')
            ->selectRaw('suppliers.id, suppliers.name as supplier_name, suppliers.supplier_type,
                         SUM(supplier_payments.amount) as total_paid,
                         COUNT(supplier_payments.id) as payment_count')
            ->groupBy('suppliers.id', 'suppliers.name', 'suppliers.supplier_type')
            ->orderByDesc('total_paid')
            ->limit(5)
            ->get();

        // ── Payment method breakdown ──────────────────────────────────────────
        $paymentMethodStats = SupplierPayment::selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        // ── Suppliers with outstanding due (highest first) ────────────────────
        $suppliersWithDue = Supplier::where('total_due', '>', 0)
            ->orderByDesc('total_due')
            ->get()
            ->each(function ($supplier) {
                $supplier->lastPayment = SupplierPayment::where('supplier_id', $supplier->id)
                    ->orderByDesc('date')
                    ->first();
            });

        // ── Monthly trend (last 8 months) ─────────────────────────────────────
        $monthlyTrend = SupplierPayment::selectRaw("DATE_FORMAT(date, '%Y-%m') as month, SUM(amount) as total")
            ->where('date', '>=', now()->subMonths(7)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('pos.payments.index', compact(
            'payments',
            'totalPaid',
            'thisMonthPaid',
            'totalOutstandingDue',
            'topSuppliers',
            'paymentMethodStats',
            'suppliersWithDue',
            'monthlyTrend'
        ));
    }

    public function create()
    {
        $suppliers      = Supplier::all();
        $businesses     = Business::all();
        $paymentMethods = ['cash', 'bank', 'khalti_external', 'khalti', 'esewa'];

        return view('pos.payments.create', compact('suppliers', 'businesses', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date'              => 'required|date',
            'business_account'  => 'nullable|exists:businesses,id',
            'supplier_id'       => 'required|exists:suppliers,id',
            'amount'            => 'required|numeric|min:0',
            'payment_method'    => 'required|in:cash,bank,khalti_external,khalti,esewa',
            'payment_reference' => 'nullable|string|max:255',
            'bank_charge'       => 'nullable|numeric|min:0',
            'tds_applicable'    => 'boolean',
            'note'              => 'nullable|string|max:1000',
            'payment_type'      => 'nullable|in:external,integrated',
        ]);

        $validated['bank_charge']    = $validated['bank_charge'] ?? 0;
        $validated['tds_applicable'] = $request->has('tds_applicable');
        $paymentMethod               = $validated['payment_method'];

        DB::transaction(function () use ($validated, $paymentMethod) {
            $supplierPayment = SupplierPayment::create($validated);

            $supplier = Supplier::find($validated['supplier_id']);
            if ($supplier) {
                $supplier->decrement('total_due', $validated['amount']);
            }

            // Income model created event handles business balance automatically
            // Negative amount_received = expense = balance decreases
            Income::create([
                'transaction_date' => $validated['date'],
                'customer_id'      => null,
                'business_id'      => $validated['business_account'],
                'amount_received'  => -$validated['amount'],
                'payment_method'   => $paymentMethod,
                'reference_no'     => 'PAY-' . $supplierPayment->id,
                'notes'            => 'Supplier payment to ' . $supplier->name . ': ' . ($validated['note'] ?? ''),
            ]);
        });

        return redirect()->route('pos.supplier-payments.index')
            ->with('success', '✅ Supplier payment created successfully.');
    }

    public function show(SupplierPayment $supplierPayment)
    {
        return view('pos.payments.show', compact('supplierPayment'));
    }

    public function edit(SupplierPayment $supplierPayment)
    {
        $suppliers      = Supplier::all();
        $businesses     = Business::all();
        $paymentMethods = ['cash', 'bank', 'khalti_external', 'khalti', 'esewa'];

        return view('pos.payments.edit', compact('supplierPayment', 'suppliers', 'businesses', 'paymentMethods'));
    }

    public function update(Request $request, SupplierPayment $supplierPayment)
    {
        $validated = $request->validate([
            'date'              => 'required|date',
            'business_account'  => 'nullable|exists:businesses,id',
            'supplier_id'       => 'required|exists:suppliers,id',
            'amount'            => 'required|numeric|min:0',
            'payment_method'    => 'required|in:cash,bank,khalti_external,khalti,esewa',
            'payment_reference' => 'nullable|string|max:255',
            'bank_charge'       => 'nullable|numeric|min:0',
            'tds_applicable'    => 'boolean',
            'note'              => 'nullable|string|max:1000',
        ]);

        $validated['bank_charge']    = $validated['bank_charge'] ?? 0;
        $validated['tds_applicable'] = $request->has('tds_applicable');

        DB::transaction(function () use ($validated, $supplierPayment) {
            $originalAmount     = $supplierPayment->amount;
            $originalSupplierId = $supplierPayment->supplier_id;

            // Restore original supplier due
            $originalSupplier = Supplier::find($originalSupplierId);
            if ($originalSupplier) {
                $originalSupplier->increment('total_due', $originalAmount);
            }

            // Delete old income — model deleted event restores business balance
            Income::where('reference_no', 'PAY-' . $supplierPayment->id)->delete();

            // Update payment record
            $supplierPayment->update($validated);

            // Apply new supplier due deduction
            $supplier = Supplier::find($validated['supplier_id']);
            if ($supplier) {
                $supplier->decrement('total_due', $validated['amount']);
            }

            // Income model created event handles business balance automatically
            Income::create([
                'transaction_date' => $validated['date'],
                'customer_id'      => null,
                'business_id'      => $validated['business_account'],
                'amount_received'  => -$validated['amount'],
                'payment_method'   => $validated['payment_method'],
                'reference_no'     => 'PAY-' . $supplierPayment->id,
                'notes'            => 'Supplier payment to ' . $supplier->name . ': ' . ($validated['note'] ?? ''),
            ]);
        });

        return redirect()->route('pos.supplier-payments.index')
            ->with('success', '✅ Supplier payment updated successfully.');
    }

    public function destroy(SupplierPayment $supplierPayment)
    {
        DB::transaction(function () use ($supplierPayment) {
            // Restore supplier due
            $supplier = Supplier::find($supplierPayment->supplier_id);
            if ($supplier) {
                $supplier->increment('total_due', $supplierPayment->amount);
            }

            // Delete income — model deleted event restores business balance
            Income::where('reference_no', 'PAY-' . $supplierPayment->id)->delete();

            $supplierPayment->delete();
        });

        return redirect()->route('pos.supplier-payments.index')
            ->with('success', '✅ Supplier payment deleted successfully.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // EXPORT METHODS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Export supplier payment records to PDF, Excel, or CSV
     * Verify Khalti payment (legacy widget flow)
     */
    public function verifyKhalti(Request $request)
    {
        try {
            $secretKey = config('services.khalti.secret_key');

            if (!$secretKey) {
                return response()->json([
                    'success' => false,
                    'message' => 'Khalti secret key not configured.',
                ], 500);
            }

            $response = Http::withOptions(['verify' => false, 'timeout' => 30])
                ->withHeaders([
                    'Authorization' => 'Key ' . $secretKey,
                    'Content-Type'  => 'application/json',
                ])->post('https://dev.khalti.com/api/v2/epayment/lookup/', [
                    'pidx' => $request->pidx ?? $request->token,
                ]);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'Completed') {
                $this->saveKhaltiPayment($request, $data);
                return response()->json([
                    'success' => true,
                    'message' => 'Payment verified and recorded successfully.',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment not completed. Status: ' . ($data['status'] ?? 'unknown'),
                'error'   => $data,
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save Khalti payment (legacy widget flow)
     */
    private function saveKhaltiPayment(Request $request, $khaltiData)
    {
        DB::transaction(function () use ($request, $khaltiData) {
            $amount = $request->amount / 100;

            $supplierPayment = SupplierPayment::create([
                'date'              => now()->toDateString(),
                'supplier_id'       => $request->supplier_id,
                'business_account'  => $request->business_account,
                'amount'            => $amount,
                'payment_method'    => 'khalti',
                'payment_type'      => 'integrated',
                'payment_reference' => $khaltiData['transaction_id'] ?? $khaltiData['pidx'],
                'bank_charge'       => 0,
                'tds_applicable'    => false,
                'note'              => 'Khalti payment - Txn: ' . ($khaltiData['transaction_id'] ?? ''),
            ]);

            $supplier = Supplier::find($request->supplier_id);
            if ($supplier) {
                $supplier->decrement('total_due', $amount);
            }

            // Income model created event handles business balance automatically
            Income::create([
                'transaction_date' => now()->toDateString(),
                'customer_id'      => null,
                'business_id'      => $request->business_account,
                'amount_received'  => -$amount,
                'payment_method'   => 'khalti',
                'reference_no'     => 'PAY-' . $supplierPayment->id,
                'notes'            => 'Khalti supplier payment to ' . optional($supplier)->name,
            ]);
        });
    }

    /**
     * Initiate Khalti Payment — redirects to Khalti payment page
     */
    public function initiateKhalti(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'amount'      => 'required|numeric|min:1',
            'date'        => 'required|date',
        ]);

        $amountPaisa = (int) ($request->amount * 100);

        try {
            $response = Http::withOptions(['verify' => false, 'timeout' => 30])
                ->withHeaders([
                    'Authorization' => 'Key ' . config('services.khalti.secret_key'),
                    'Content-Type'  => 'application/json',
                ])->post('https://dev.khalti.com/api/v2/epayment/initiate/', [
                    'return_url'          => route('pos.khalti.callback'),
                    'website_url'         => config('app.url'),
                    'amount'              => $amountPaisa,
                    'purchase_order_id'   => 'SUPPAY-' . $request->supplier_id . '-' . time(),
                    'purchase_order_name' => 'Supplier Payment',
                    'customer_info'       => [
                        'name'  => 'Supplier Payment',
                        'email' => 'payment@grocemate.com',
                        'phone' => '9800000001',
                    ],
                ]);
        } catch (\Exception $e) {
            return back()->with('error', '❌ Could not connect to Khalti: ' . $e->getMessage());
        }

        $data = $response->json();

        if (!$response->successful() || !isset($data['payment_url'])) {
            return back()->with('error',
                '❌ Khalti Error: ' . json_encode($data) .
                ' | HTTP: ' . $response->status() .
                ' | Key starts with: ' . substr(config('services.khalti.secret_key'), 0, 8) . '...'
            );
        }

        session([
            'khalti_payment' => [
                'supplier_id'      => $request->supplier_id,
                'business_account' => $request->business_account,
                'amount'           => $request->amount,
                'date'             => $request->date,
                'note'             => $request->note,
                'bank_charge'      => $request->bank_charge ?? 0,
                'tds_applicable'   => $request->tds_applicable ?? false,
                'pidx'             => $data['pidx'],
            ]
        ]);

        session()->save();

        return redirect($data['payment_url']);
    }

    /**
     * Handle Khalti callback after payment
     */
    public function khaltiCallback(Request $request)
    {
        $pidx   = $request->pidx;
        $status = $request->status;

        if ($status !== 'Completed') {
            return redirect()->route('pos.supplier-payments.create')
                ->with('error', '❌ Khalti payment was not completed. Status: ' . $status);
        }

        try {
            $response = Http::withOptions(['verify' => false, 'timeout' => 30])
                ->withHeaders([
                    'Authorization' => 'Key ' . config('services.khalti.secret_key'),
                    'Content-Type'  => 'application/json',
                ])->post('https://dev.khalti.com/api/v2/epayment/lookup/', [
                    'pidx' => $pidx,
                ]);
        } catch (\Exception $e) {
            return redirect()->route('pos.supplier-payments.create')
                ->with('error', '❌ Khalti verification failed: ' . $e->getMessage());
        }

        $data = $response->json();

        if (!$response->successful() || ($data['status'] ?? '') !== 'Completed') {
            return redirect()->route('pos.supplier-payments.create')
                ->with('error', '❌ Khalti verification failed. Status: ' . ($data['status'] ?? 'unknown'));
        }

        $paymentInfo = session('khalti_payment');

        if (!$paymentInfo || $paymentInfo['pidx'] !== $pidx) {
            return redirect()->route('pos.supplier-payments.create')
                ->with('error', '❌ Session expired. Please try again.');
        }

        DB::transaction(function () use ($paymentInfo, $data) {
            $supplierPayment = SupplierPayment::create([
                'date'              => $paymentInfo['date'],
                'supplier_id'       => $paymentInfo['supplier_id'],
                'business_account'  => $paymentInfo['business_account'],
                'amount'            => $paymentInfo['amount'],
                'payment_method'    => 'khalti',
                'payment_type'      => 'integrated',
                'payment_reference' => $data['transaction_id'] ?? $paymentInfo['pidx'],
                'bank_charge'       => $paymentInfo['bank_charge'],
                'tds_applicable'    => $paymentInfo['tds_applicable'],
                'note'              => $paymentInfo['note'] ?? 'Khalti payment',
            ]);

            $supplier = Supplier::find($paymentInfo['supplier_id']);
            if ($supplier) {
                $supplier->decrement('total_due', $paymentInfo['amount']);
            }

            // ✅ Income model created event handles business balance
            // NO manual $business->decrement() here — that was causing double deduction
            Income::create([
                'transaction_date' => $paymentInfo['date'],
                'customer_id'      => null,
                'business_id'      => $paymentInfo['business_account'],
                'amount_received'  => -$paymentInfo['amount'],
                'payment_method'   => 'khalti',
                'reference_no'     => 'PAY-' . $supplierPayment->id,
                'notes'            => 'Khalti supplier payment - ' . ($data['transaction_id'] ?? ''),
            ]);
        });

        session()->forget('khalti_payment');

        return redirect()->route('pos.supplier-payments.index')
            ->with('success', '✅ Khalti payment of NPR ' . $paymentInfo['amount'] . ' successful and recorded!');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // ESEWA
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Initiate eSewa Payment — renders a self-submitting HTML form
     */
    public function initiateEsewa(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'amount'      => 'required|numeric|min:1',
            'date'        => 'required|date',
        ]);

        $totalAmount     = number_format($request->amount, 2, '.', '');
        $transactionUuid = 'SUPPAY-' . $request->supplier_id . '-' . time();
        $productCode     = config('services.esewa.product_code');
        $secretKey       = config('services.esewa.secret_key');

        $message   = "total_amount={$totalAmount},transaction_uuid={$transactionUuid},product_code={$productCode}";
        $signature = base64_encode(hash_hmac('sha256', $message, $secretKey, true));

        session([
            'esewa_payment' => [
                'supplier_id'      => $request->supplier_id,
                'business_account' => $request->business_account,
                'amount'           => $request->amount,
                'date'             => $request->date,
                'note'             => $request->note,
                'bank_charge'      => $request->bank_charge ?? 0,
                'tds_applicable'   => $request->tds_applicable ?? false,
                'transaction_uuid' => $transactionUuid,
                'total_amount'     => $totalAmount,
            ]
        ]);

        session()->save();

        $paymentUrl = config('services.esewa.payment_url');
        $successUrl = route('pos.esewa.callback');
        $failureUrl = route('pos.esewa.callback');

        return view('pos.payments.esewa_redirect', compact(
            'paymentUrl', 'totalAmount', 'transactionUuid',
            'productCode', 'signature', 'successUrl', 'failureUrl'
        ));
    }

    /**
     * Handle eSewa callback after payment
     */
    public function esewaCallback(Request $request)
    {
        $rawData = $request->query('data');

        if (!$rawData) {
            return redirect()->route('pos.supplier-payments.create')
                ->with('error', '❌ eSewa payment was cancelled or failed.');
        }

        $decoded = json_decode(base64_decode($rawData), true);

        if (!$decoded) {
            return redirect()->route('pos.supplier-payments.create')
                ->with('error', '❌ Invalid payment data from eSewa.');
        }

        if (($decoded['status'] ?? '') !== 'COMPLETE') {
            return redirect()->route('pos.supplier-payments.create')
                ->with('error', '❌ eSewa payment not completed. Status: ' . ($decoded['status'] ?? 'unknown'));
        }

        // Verify HMAC signature
        $secretKey       = config('services.esewa.secret_key');
        $signedFields    = explode(',', $decoded['signed_field_names']);
        $signatureString = collect($signedFields)
            ->map(fn($field) => "{$field}={$decoded[$field]}")
            ->implode(',');

        $expectedSignature = base64_encode(hash_hmac('sha256', $signatureString, $secretKey, true));

        if ($expectedSignature !== $decoded['signature']) {
            return redirect()->route('pos.supplier-payments.create')
                ->with('error', '❌ eSewa signature verification failed.');
        }

        $paymentInfo = session('esewa_payment');

        if (!$paymentInfo) {
            return redirect()->route('pos.supplier-payments.create')
                ->with('error', '❌ Session expired. Transaction ID: ' . ($decoded['transaction_uuid'] ?? 'N/A'));
        }

        // In production — verify with eSewa Status API
        // In local dev — HMAC signature above is sufficient (DNS issue with uat.esewa.com.np)
        if (app()->environment('production')) {
            try {
                $verify     = Http::withOptions(['verify' => false, 'timeout' => 30])
                    ->get(config('services.esewa.status_url'), [
                        'product_code'     => config('services.esewa.product_code'),
                        'total_amount'     => $decoded['total_amount'],
                        'transaction_uuid' => $decoded['transaction_uuid'],
                    ]);
                $verifyData = $verify->json();

                if (($verifyData['status'] ?? '') !== 'COMPLETE') {
                    return redirect()->route('pos.supplier-payments.create')
                        ->with('error', '❌ eSewa API verification failed: ' . ($verifyData['status'] ?? 'unknown'));
                }
            } catch (\Exception $e) {
                return redirect()->route('pos.supplier-payments.create')
                    ->with('error', '❌ eSewa verification error: ' . $e->getMessage());
            }
        }

        DB::transaction(function () use ($paymentInfo, $decoded) {
            $supplierPayment = SupplierPayment::create([
                'date'              => $paymentInfo['date'],
                'supplier_id'       => $paymentInfo['supplier_id'],
                'business_account'  => $paymentInfo['business_account'],
                'amount'            => $paymentInfo['amount'],
                'payment_method'    => 'esewa',
                'payment_type'      => 'integrated',
                'payment_reference' => $decoded['transaction_uuid'],
                'bank_charge'       => $paymentInfo['bank_charge'],
                'tds_applicable'    => $paymentInfo['tds_applicable'],
                'note'              => $paymentInfo['note'] ?? 'eSewa payment',
            ]);

            $supplier = Supplier::find($paymentInfo['supplier_id']);
            if ($supplier) {
                $supplier->decrement('total_due', $paymentInfo['amount']);
            }

            // ✅ Income model created event handles business balance
            // NO manual $business->decrement() here
            Income::create([
                'transaction_date' => $paymentInfo['date'],
                'customer_id'      => null,
                'business_id'      => $paymentInfo['business_account'],
                'amount_received'  => -$paymentInfo['amount'],
                'payment_method'   => 'esewa',
                'reference_no'     => 'PAY-' . $supplierPayment->id,
                'notes'            => 'eSewa supplier payment - ' . $decoded['transaction_uuid'],
            ]);
        });

        session()->forget('esewa_payment');

        return redirect()->route('pos.supplier-payments.index')
            ->with('success', '✅ eSewa payment of NPR ' . $paymentInfo['amount'] . ' successful and recorded!');
    }

    // ─────────────────────────────────────────────────────────────────
    // EXPORT METHODS
    // ─────────────────────────────────────────────────────────────────

    /**
     * Export supplier payment records to PDF, Excel, or CSV
     */
    public function export($type, Request $request)
    {
        // Get date filters (using 'from' and 'to' like purchase export)
        $from = $request->get('from');
        $to = $request->get('to');

        // Build query with same filters as index
        $query = SupplierPayment::with('supplier');

        // Apply search filter
        if ($request->has('q') && !empty($request->q)) {
            $search = $request->q;
            $query->where(function ($sub) use ($search) {
                $sub->whereHas('supplier', fn($sq) => $sq->where('name', 'like', "%{$search}%"))
                    ->orWhere('payment_reference', 'like', "%{$search}%");
            });
        }

        // Apply date filters (using from/to like purchase export)
        if ($from && $to) {
            try {
                $fromDate = \Carbon\Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
                $toDate = \Carbon\Carbon::createFromFormat('Y-m-d', $to)->endOfDay();
                $query->whereBetween('date', [$fromDate, $toDate]);
            } catch (\Exception $e) {
                // Invalid date format, skip date filtering
            }
        } elseif ($from) {
            try {
                $fromDate = \Carbon\Carbon::createFromFormat('Y-m-d', $from)->startOfDay();
                $query->whereDate('date', '>=', $fromDate);
            } catch (\Exception $e) {
                // Invalid date format, skip date filtering
            }
        } elseif ($to) {
            try {
                $toDate = \Carbon\Carbon::createFromFormat('Y-m-d', $to)->endOfDay();
                $query->whereDate('date', '<=', $toDate);
            } catch (\Exception $e) {
                // Invalid date format, skip date filtering
            }
        }

        // Apply payment method filter
        if ($request->has('payment_method') && !empty($request->payment_method)) {
            $query->where('payment_method', $request->payment_method);
        }

        // Get filtered results
        $payments = $query->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        if ($type === 'pdf') {
            $filename = 'supplier_payments_report_' . now()->format('Y-m-d') . '.pdf';
            
            $html = view('pos.payments.export-pdf', [
                'payments' => $payments,
                'from' => $from,
                'to' => $to
            ])->render();
            
            // Use simple PDF generation with DOMPDF style (same as purchase export)
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
            $filename = 'supplier_payments_report_' . now()->format('Y-m-d') . '.xlsx';
            
            // Generate HTML table that Excel can open (same as purchase export)
            $html = view('pos.payments.export-excel', [
                'payments' => $payments,
                'from' => $from,
                'to' => $to
            ])->render();
            
            return response($html, 200, [
                'Content-Type' => 'application/vnd.ms-excel',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);
        }

        if ($type === 'csv') {
            $filename = 'supplier_payments_report_' . now()->format('Y-m-d') . '.csv';
            
            // Generate CSV content
            $csv = "Date,Reference,Supplier,Business Account,Payment Method,Payment Type,Amount,Bank Charge,TDS Applicable,Notes\n";
            
            foreach ($payments as $payment) {
                $csv .= '"' . \Carbon\Carbon::parse($payment->date)->format('Y-m-d') . '",';
                $csv .= '"PAY-' . str_pad($payment->id, 4, '0', STR_PAD_LEFT) . '",';
                $csv .= '"' . ($payment->supplier->name ?? 'N/A') . '",';
                $csv .= '"' . ($payment->business_account ?? 'N/A') . '",';
                $csv .= '"' . ucfirst($payment->payment_method) . '",';
                $csv .= '"' . ucfirst($payment->payment_type ?? 'external') . '",';
                $csv .= $payment->amount . ',';
                $csv .= $payment->bank_charge . ',';
                $csv .= '"' . ($payment->tds_applicable ? 'Yes' : 'No') . '",';
                $csv .= '"' . ($payment->note ?? '-') . '"' . "\n";
            }
            
            return response($csv, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ]);
        }

        return redirect()->back()->with('error', 'Invalid export type');
    }
}