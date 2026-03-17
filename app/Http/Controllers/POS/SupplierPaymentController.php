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
<<<<<<< HEAD
            ->orderBy('created_at', 'desc')  // Latest creation first
            ->orderBy('date', 'desc')  // Then by payment date
            ->orderBy('id', 'desc')  // Finally by ID as fallback
=======
            ->orderBy('created_at', 'desc')
>>>>>>> df2ea1df4a76506cf0d8fa3eaeaf82c0dc4216ec
            ->paginate(15);

        return view('pos.payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $suppliers = Supplier::all();
        $businesses = Business::all();
<<<<<<< HEAD
        $paymentMethods = ['cash', 'bank', 'khalti_external', 'khalti', 'esewa'];
=======
        $paymentMethods = ['cash', 'bank', 'Khalti'];
>>>>>>> df2ea1df4a76506cf0d8fa3eaeaf82c0dc4216ec
        
        return view('pos.payments.create', compact('suppliers', 'businesses', 'paymentMethods'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'business_account' => 'nullable|exists:businesses,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'amount' => 'required|numeric|min:0',
<<<<<<< HEAD
            'payment_method' => 'required|in:cash,bank,khalti_external,khalti,esewa',
=======
            'payment_type' => 'required|in:external,integrated',
            'payment_method_external' => 'required_if:payment_type,external|in:cash,bank,esewa_external,khalti_external',
            'payment_method_integrated' => 'required_if:payment_type,integrated|in:khalti_integrated',
>>>>>>> df2ea1df4a76506cf0d8fa3eaeaf82c0dc4216ec
            'payment_reference' => 'nullable|string|max:255',
            'bank_charge' => 'nullable|numeric|min:0',
            'tds_applicable' => 'boolean',
            'note' => 'nullable|string|max:1000',
            'payment_type' => 'required|in:external,integrated',
        ]);

        $validated['bank_charge'] = $validated['bank_charge'] ?? 0;
        $validated['tds_applicable'] = $request->has('tds_applicable');

<<<<<<< HEAD
        // Handle different payment types
        if ($validated['payment_type'] === 'external') {
            return $this->processExternalPayment($validated);
        } else {
            return $this->processIntegratedPayment($validated);
        }
    }

    /**
     * Process external payment (save immediately)
     */
    private function processExternalPayment($validated)
    {
=======
        // Determine the actual payment method based on payment type
        if ($validated['payment_type'] === 'external') {
            $paymentMethod = $validated['payment_method_external'];
        } else {
            $paymentMethod = $validated['payment_method_integrated'];
        }

        // Add the payment_method to validated data for database storage
        $validated['payment_method'] = $paymentMethod;

>>>>>>> df2ea1df4a76506cf0d8fa3eaeaf82c0dc4216ec
        // Use database transaction to ensure data integrity
        DB::transaction(function () use ($validated, $paymentMethod) {
            // Create the supplier payment
            $supplierPayment = SupplierPayment::create($validated);

            // 1. Decrease supplier due amount
            $supplier = Supplier::find($validated['supplier_id']);
            if ($supplier) {
                $supplier->decrement('total_due', $validated['amount']);
            }

            // Business account balance is automatically updated by Income model's created event

            // 3. Create a negative income entry (expense) to track the payment
            Income::create([
                'transaction_date' => $validated['date'],
                'customer_id' => null, // Not applicable for supplier payments
                'business_id' => $validated['business_account'],
                'amount_received' => -$validated['amount'], // Negative amount for expense
                'payment_method' => $paymentMethod,
                'reference_no' => 'PAY-' . $supplierPayment->id,
                'notes' => 'Supplier payment to ' . $supplier->name . ': ' . ($validated['note'] ?? '')
            ]);
        });

        return redirect()->route('pos.supplier-payments.index')
            ->with('success', 'Supplier payment created successfully and financial records updated.');
    }

    /**
     * Process integrated payment (Khalti - wait for payment verification)
     */
    private function processIntegratedPayment($validated)
    {
        // For Khalti payments, don't save immediately
        // Payment will be saved after successful Khalti verification
        if ($validated['payment_method'] === 'khalti') {
            // Return response that tells frontend to initiate Khalti payment
            return response()->json([
                'success' => false,
                'message' => 'Please complete payment via Khalti',
                'action' => 'initiate_khalti_payment',
                'data' => $validated
            ]);
        }
        
        // For other integrated payments (eSewa, etc.), you might want to save immediately
        // For now, we'll save directly to database as before
        $paymentId = 'SUPPAY-' . time();

        DB::transaction(function () use ($validated, $paymentId) {
            // Add payment ID to validated data
            $validated['payment_reference'] = $paymentId;

            // Create the supplier payment
            $supplierPayment = SupplierPayment::create($validated);

            // 1. Decrease supplier due amount
            $supplier = Supplier::find($validated['supplier_id']);
            if ($supplier) {
                $supplier->decrement('total_due', $validated['amount']);
            }

            // Business account balance is automatically updated by Income model's created event

            // 3. Create a negative income entry (expense) to track the payment
            Income::create([
                'transaction_date' => $validated['date'],
                'customer_id' => null, // Not applicable for supplier payments
                'business_id' => $validated['business_account'],
                'amount_received' => -$validated['amount'], // Negative amount for expense
                'payment_method' => $validated['payment_method'],
                'reference_no' => 'PAY-' . $supplierPayment->id,
                'notes' => 'Supplier payment to ' . $supplier->name . ': ' . ($validated['note'] ?? '') . ' [PID: ' . $paymentId . ']'
            ]);
        });

        return redirect()->route('pos.supplier-payments.index')
            ->with('success', 'Integrated payment processed successfully with Payment ID: ' . $paymentId);
    }

    /**
     * Display the specified resource.
     */
    public function show(SupplierPayment $supplierPayment)
    {
        return view('pos.payments.show', compact('supplierPayment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SupplierPayment $supplierPayment)
    {
        $suppliers = Supplier::all();
        $businesses = Business::all();
        $paymentMethods = ['cash', 'bank', 'khalti_external', 'khalti'];
        
        return view('pos.payments.edit', compact('supplierPayment', 'suppliers', 'businesses', 'paymentMethods'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SupplierPayment $supplierPayment)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'business_account' => 'nullable|exists:businesses,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,bank,khalti_external,khalti,esewa',
            'payment_reference' => 'nullable|string|max:255',
            'bank_charge' => 'nullable|numeric|min:0',
            'tds_applicable' => 'boolean',
            'note' => 'nullable|string|max:1000',
        ]);

        $validated['bank_charge'] = $validated['bank_charge'] ?? 0;
        $validated['tds_applicable'] = $request->has('tds_applicable');

        // Use database transaction to ensure data integrity
        DB::transaction(function () use ($validated, $supplierPayment) {
            // Get original payment details for reversal
            $originalAmount = $supplierPayment->amount;
            $originalSupplierId = $supplierPayment->supplier_id;
            $originalBusinessId = $supplierPayment->business_account;

            // 1. Reverse the original financial changes
            // Restore supplier due amount
            $originalSupplier = Supplier::find($originalSupplierId);
            if ($originalSupplier) {
                $originalSupplier->increment('total_due', $originalAmount);
            }

            // Business account balance is automatically restored by Income model's deleted event

            // Delete the original income entry
            Income::where('reference_no', 'PAY-' . $supplierPayment->id)->delete();

            // Update the supplier payment
            $supplierPayment->update($validated);

            // 2. Apply new financial changes
            $supplier = Supplier::find($validated['supplier_id']);
            if ($supplier) {
                $supplier->decrement('total_due', $validated['amount']);
            }

            // Business account balance is automatically updated by Income model's events

            // Create new income entry
            Income::create([
                'transaction_date' => $validated['date'],
                'customer_id' => null,
                'business_id' => $validated['business_account'],
                'amount_received' => -$validated['amount'],
                'payment_method' => $validated['payment_method'],
                'reference_no' => 'PAY-' . $supplierPayment->id,
                'notes' => 'Supplier payment to ' . $supplier->name . ': ' . ($validated['note'] ?? '')
            ]);
        });

        return redirect()->route('pos.supplier-payments.index')
            ->with('success', 'Supplier payment updated successfully and financial records updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SupplierPayment $supplierPayment)
    {
        // Use database transaction to ensure data integrity
        DB::transaction(function () use ($supplierPayment) {
            // 1. Restore supplier due amount
            $supplier = Supplier::find($supplierPayment->supplier_id);
            if ($supplier) {
                $supplier->increment('total_due', $supplierPayment->amount);
            }

            // Business account balance is automatically restored by Income model's deleted event

            // 3. Delete the income entry
            Income::where('reference_no', 'PAY-' . $supplierPayment->id)->delete();

            // 4. Delete the supplier payment
            $supplierPayment->delete();
        });

        return redirect()->route('pos.supplier-payments.index')
            ->with('success', 'Supplier payment deleted successfully and financial records restored.');
    }

    /**
     * Verify Khalti payment
     */
    public function verifyKhalti(Request $request)
{
    try {
        $secretKey = config('services.khalti.secret_key');
        
        if (!$secretKey) {
            return response()->json([
                'success' => false,
                'message' => 'Khalti secret key not configured. Please add KHALTI_SECRET_KEY to .env file'
            ], 500);
        }

        // Support both old SDK (token) and new SDK (pidx)
        $response = Http::withOptions([
            'verify' => false, // Only for development
        ])->withHeaders([
            'Authorization' => 'Key ' . $secretKey,
            'Content-Type'  => 'application/json',
        ])->post('https://dev.khalti.com/api/v2/epayment/lookup/', [
            'pidx' => $request->pidx ?? $request->token,
        ]);

        $data = $response->json();

        if ($response->successful() && 
            isset($data['status']) && 
            $data['status'] === 'Completed') {

            $this->saveKhaltiPayment($request, $data);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified and recorded successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment not completed. Status: ' . ($data['status'] ?? 'unknown'),
            'error'   => $data
        ], 400);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Save Khalti payment to database
     */
    private function saveKhaltiPayment(Request $request, $khaltiData)
{
    DB::transaction(function () use ($request, $khaltiData) {
        $paymentData = [
            'date'              => now()->toDateString(),
            'supplier_id'       => $request->supplier_id,
            'business_account'  => $request->business_account,
            'amount'            => $request->amount / 100,
            'payment_method'    => 'khalti',
            'payment_type'      => 'integrated',
            
            // ✅ v2 uses transaction_id
            'payment_reference' => $khaltiData['transaction_id'] ?? $khaltiData['pidx'],
            'bank_charge'       => 0,
            'tds_applicable'    => false,
            'note'              => 'Khalti payment - Txn: ' . ($khaltiData['transaction_id'] ?? ''),
        ];

        $supplierPayment = SupplierPayment::create($paymentData);

        $supplier = Supplier::find($request->supplier_id);
        if ($supplier) {
            $supplier->decrement('total_due', $paymentData['amount']);
        }

        // Business account balance is automatically updated by Income model's created event

        Income::create([
            'transaction_date' => $paymentData['date'],
            'customer_id'      => null,
            'business_id'      => $request->business_account,
            'amount_received'  => -$paymentData['amount'],
            'payment_method'   => 'khalti',
            'reference_no'     => 'PAY-' . $supplierPayment->id,
            'notes'            => 'Khalti supplier payment to ' . $supplier->name,
        ]);
    });
}
/**
 * Initiate Khalti Payment - redirects to Khalti
 */
public function initiateKhalti(Request $request)
{
    $request->validate([
        'supplier_id' => 'required|exists:suppliers,id',
        'amount'      => 'required|numeric|min:1',
        'date'        => 'required|date',
    ]);

    $amountPaisa = (int) ($request->amount * 100);

    $response = Http::withOptions([
    'verify'  => false,
    'timeout' => 30,
     ])->withHeaders([
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

    $data = $response->json();

    if ($response->successful() && isset($data['payment_url'])) {
        // Store payment data in session to use after callback
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

        // Redirect user to Khalti payment page
        return redirect($data['payment_url']);
    }

    return back()->with('error', 'Could not initiate Khalti payment: ' . ($data['detail'] ?? 'Unknown error'));
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
            ->with('error', 'Payment was not completed. Status: ' . $status);
    }

    // Verify with Khalti lookup API
    $response = Http::withHeaders([
        'Authorization' => 'Key ' . config('services.khalti.secret_key'),
        'Content-Type'  => 'application/json',
    ])->post('https://dev.khalti.com/api/v2/epayment/lookup/', [
        'pidx' => $pidx,
    ]);

    $data = $response->json();

    if (!$response->successful() || ($data['status'] ?? '') !== 'Completed') {
        return redirect()->route('pos.supplier-payments.create')
            ->with('error', 'Payment verification failed.');
    }

    // Get stored session data
    $paymentInfo = session('khalti_payment');

    if (!$paymentInfo || $paymentInfo['pidx'] !== $pidx) {
        return redirect()->route('pos.supplier-payments.create')
            ->with('error', 'Session expired. Please try again.');
    }

    // Save to database
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

        if (!empty($paymentInfo['business_account'])) {
            $business = Business::find($paymentInfo['business_account']);
            if ($business) {
                $business->decrement('balance', $paymentInfo['amount']);
            }
        }

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

    // Clear session
    session()->forget('khalti_payment');

    return redirect()->route('pos.supplier-payments.index')
        ->with('success', '✅ Khalti payment successful and recorded!');
}

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

    // Generate HMAC-SHA256 signature
    $message   = "total_amount={$totalAmount},transaction_uuid={$transactionUuid},product_code={$productCode}";
    $signature = base64_encode(hash_hmac('sha256', $message, $secretKey, true));

    // Store in session for callback verification
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
        ]
    ]);

    // Render auto-submit form to eSewa
    $paymentUrl  = config('services.esewa.payment_url');
    $successUrl  = route('pos.esewa.callback') . '?status=success';
    $failureUrl  = route('pos.esewa.callback') . '?status=failed';

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
    $status = $request->query('status');

    if ($status !== 'success') {
        return redirect()->route('pos.supplier-payments.create')
            ->with('error', '❌ eSewa payment was cancelled or failed.');
    }

    // Decode the data param returned by eSewa
    $rawData = $request->query('data');
    if (!$rawData) {
        return redirect()->route('pos.supplier-payments.create')
            ->with('error', '❌ No payment data received from eSewa.');
    }

    $decoded = json_decode(base64_decode($rawData), true);

    if (!$decoded) {
        return redirect()->route('pos.supplier-payments.create')
            ->with('error', '❌ Invalid payment data from eSewa.');
    }

    // Verify signature
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

    // Verify with eSewa Status API
    $paymentInfo = session('esewa_payment');

    if (!$paymentInfo) {
        return redirect()->route('pos.supplier-payments.create')
            ->with('error', '❌ Session expired. Please try again.');
    }

    $verify = Http::get(config('services.esewa.status_url'), [
        'product_code'     => config('services.esewa.product_code'),
        'total_amount'     => $decoded['total_amount'],
        'transaction_uuid' => $decoded['transaction_uuid'],
    ]);

    $verifyData = $verify->json();

    if (($verifyData['status'] ?? '') !== 'COMPLETE') {
        return redirect()->route('pos.supplier-payments.create')
            ->with('error', '❌ eSewa payment verification failed: ' . ($verifyData['status'] ?? 'unknown'));
    }

    // ✅ Save to database
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
        ->with('success', '✅ eSewa payment successful and recorded!');
}
}