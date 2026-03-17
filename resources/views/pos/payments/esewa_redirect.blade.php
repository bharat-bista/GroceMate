<!DOCTYPE html>
<html>
<head>
    <title>Redirecting to eSewa...</title>
</head>
<body>
    <p style="text-align:center; margin-top: 50px; font-family: sans-serif;">
        Redirecting to eSewa payment... Please wait.
    </p>

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
        document.getElementById('esewa-form').submit();
    </script>
</body>
</html>