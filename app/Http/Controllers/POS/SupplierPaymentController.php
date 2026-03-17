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
    public function index()
    {
        $payments = SupplierPayment::with('supplier')
            ->orderBy('created_at', 'desc')
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('pos.payments.index', compact('payments'));
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
    // KHALTI
    // ─────────────────────────────────────────────────────────────────────────

    /**
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
}