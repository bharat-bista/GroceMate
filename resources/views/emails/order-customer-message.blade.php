<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message about Order {{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 100%);
            color: white;
            padding: 24px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .header p {
            margin: 8px 0 0;
            opacity: 0.9;
        }
        .message-box {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }
        .message-box h2 {
            margin: 0 0 10px;
            font-size: 16px;
            color: #1f2937;
        }
        .message-text {
            white-space: pre-line;
            color: #374151;
            font-size: 14px;
        }
        .order-info {
            border-top: 1px solid #e5e7eb;
            padding-top: 16px;
            margin-top: 16px;
            font-size: 14px;
            color: #4b5563;
        }
        .order-info strong {
            color: #1f2937;
        }
        .footer {
            text-align: center;
            padding: 18px;
            color: #6b7280;
            font-size: 13px;
        }
        .footer a {
            color: #2e7d32;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Message from GroceMate</h1>
        <p>Regarding Order {{ $order->order_number }}</p>
    </div>

    <div class="message-box">
        <h2>Your message</h2>
        <div class="message-text">{{ $messageBody }}</div>

        <div class="order-info">
            <div><strong>Order Number:</strong> {{ $order->order_number }}</div>
            <div><strong>Order Date:</strong> {{ $order->created_at->format('M d, Y h:i A') }}</div>
            <div><strong>Customer Name:</strong> {{ $order->customer_name }}</div>
        </div>
    </div>

    <div class="footer">
        Thank you for shopping with GroceMate. If you have any questions, reply to this email.
    </div>
</body>
</html>
