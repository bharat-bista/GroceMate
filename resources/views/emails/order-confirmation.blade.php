<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order {{ $order->order_number }}</title>
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
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .order-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .order-info h2 {
            margin: 0 0 15px;
            color: #1f2937;
            font-size: 18px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #6b7280;
        }
        .info-value {
            font-weight: 600;
            color: #1f2937;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background: #f3f4f6;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .items-table tr:last-child td {
            border-bottom: none;
        }
        .total-section {
            background: #ecfdf5;
            border: 1px solid #d1fae5;
            border-radius: 10px;
            padding: 20px;
            text-align: right;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
        }
        .total-label {
            color: #6b7280;
        }
        .total-value {
            font-weight: 700;
            color: #1f2937;
        }
        .total-final {
            font-size: 18px;
            color: #059669;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-processing { background: #dbeafe; color: #1e40af; }
        .status-verified { background: #d1fae5; color: #065f46; }
        .status-cod { background: #e0e7ff; color: #3730a3; }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6b7280;
            font-size: 14px;
        }
        .footer a {
            color: #2e7d32;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>
            @if($type === 'payment_verified')
                <i class="fas fa-check-circle"></i> Payment Verified!
            @else
                <i class="fas fa-box-open"></i> Order Confirmed
            @endif
        </h1>
        <p>Thank you for your order with GroceMate</p>
    </div>

    <div class="order-info">
        <h2>Order Details</h2>
        <div class="info-row">
            <span class="info-label">Order Number</span>
            <span class="info-value">{{ $order->order_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Order Date</span>
            <span class="info-value">{{ $order->created_at->format('M d, Y h:i A') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Customer Name</span>
            <span class="info-value">{{ $order->customer_name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Phone</span>
            <span class="info-value">{{ $order->customer_phone }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Delivery Type</span>
            <span class="info-value">{{ ucfirst($order->delivery_type) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Delivery Address</span>
            <span class="info-value">{{ $order->delivery_address }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Payment Method</span>
            <span class="info-value">
                @if($order->payment_method === 'esewa')
                    eSewa
                @elseif($order->payment_method === 'connectips')
                    Connect IPS
                @else
                    Cash on Delivery
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Payment Status</span>
            <span class="info-value">
                @if($order->payment_status === 'verified')
                    <span class="status-badge status-verified">Verified</span>
                @elseif($order->payment_status === 'pending')
                    <span class="status-badge status-pending">Pending</span>
                @else
                    <span class="status-badge status-cod">COD</span>
                @endif
            </span>
        </div>
    </div>

    <h3>Order Items</h3>
    <table class="items-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td>{{ $item->product_name }}</td>
                <td>Rs. {{ number_format($item->price, 0) }}</td>
                <td>{{ $item->quantity }}</td>
                <td>Rs. {{ number_format($item->subtotal, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span class="total-label">Subtotal</span>
            <span class="total-value">Rs. {{ number_format($order->subtotal, 0) }}</span>
        </div>
        <div class="total-row">
            <span class="total-label">Delivery Charge</span>
            <span class="total-value">Rs. {{ number_format($order->delivery_charge, 0) }}</span>
        </div>
        <div class="total-row">
            <span class="total-label total-final">Total Amount</span>
            <span class="total-value total-final">Rs. {{ number_format($order->total_amount, 0) }}</span>
        </div>
    </div>

    <div class="footer">
        <p>You can track your order status by Nepal Can Move.</p>
        <p>If you have any questions, please contact us.</p>
        <p>Thank you for shopping with <strong>GroceMate</strong>!</p>
    </div>
</body>
</html>