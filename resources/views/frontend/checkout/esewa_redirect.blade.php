<!DOCTYPE html>
<html>
<head>
    <title>Redirecting to eSewa...</title>
    <style>
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        }
        .box { 
            text-align: center; 
            padding: 48px; 
            background: white; 
            border-radius: 20px; 
            box-shadow: 0 10px 40px rgba(0,0,0,0.1); 
            border: 1px solid #e5e7eb;
            max-width: 400px;
        }
        .spinner { 
            width: 48px; 
            height: 48px; 
            border: 4px solid #dcfce7; 
            border-top-color: #22c55e; 
            border-radius: 50%; 
            animation: spin 0.8s linear infinite; 
            margin: 0 auto 20px; 
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 20px;
        }
        h3 { 
            color: #15803d; 
            margin: 0 0 12px; 
            font-size: 1.3rem;
            font-weight: 600;
        }
        p { 
            color: #6b7280; 
            margin: 0; 
            font-size: 1rem;
            line-height: 1.5;
        }
        .amount {
            background: #f0fdf4;
            color: #15803d;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            margin: 16px 0;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="box">
        <div class="logo">eSewa</div>
        <div class="spinner"></div>
        <h3>Redirecting to eSewa...</h3>
        <p>Please wait, do not close this window.</p>
        @if(isset($totalAmount))
        <div class="amount">Rs. {{ number_format($totalAmount, 2) }}</div>
        @endif
        <p style="margin-top: 12px; font-size: 0.9rem;">You will be redirected to secure payment gateway</p>
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
        }, 500);
    </script>
</body>
</html>
