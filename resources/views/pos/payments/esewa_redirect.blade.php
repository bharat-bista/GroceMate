<!DOCTYPE html>
<html>
<head>
    <title>Redirecting to eSewa...</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; 
               align-items: center; height: 100vh; margin: 0; background: #f0fdf4; }
        .box { text-align: center; padding: 40px; background: white; 
               border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .spinner { width: 40px; height: 40px; border: 4px solid #d1fae5; 
                   border-top-color: #10b981; border-radius: 50%; 
                   animation: spin 0.8s linear infinite; margin: 0 auto 16px; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="box">
        <div class="spinner"></div>
        <h3 style="color: #065f46; margin: 0 0 8px;">Redirecting to eSewa...</h3>
        <p style="color: #6b7280; margin: 0;">Please wait, do not close this window.</p>
    </div>

    <form id="esewa-form" method="POST" action="{{ $paymentUrl }}">
        <input type="hidden" name="amount"                   value="{{ $totalAmount }}">
        <input type="hidden" name="tax_amount"               value="0">
        <input type="hidden" name="total_amount"             value="{{ $totalAmount }}">
        <input type="hidden" name="transaction_uuid"         value="{{ $transactionUuid }}">
        <input type="hidden" name="product_code"             value="{{ $productCode }}">
        <input type="hidden" name="product_service_charge"   value="0">
        <input type="hidden" name="product_delivery_charge"  value="0">
        <input type="hidden" name="success_url"              value="{{ $successUrl }}">
        <input type="hidden" name="failure_url"              value="{{ $failureUrl }}">
        <input type="hidden" name="signed_field_names"       value="total_amount,transaction_uuid,product_code">
        <input type="hidden" name="signature"                value="{{ $signature }}">
    </form>

    <script>
        // Small delay to ensure session is written before form submits
        setTimeout(function() {
            document.getElementById('esewa-form').submit();
        }, 300);
    </script>
</body>
</html>