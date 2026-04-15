<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        .meta { color: #666; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <h1>Ecommerce Orders Report</h1>

    @if($from && $to)
        <div class="meta">Date Range: {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</div>
    @endif

    @if(!empty($search))
        <div class="meta">Search: {{ $search }}</div>
    @endif

    @if(!empty($status))
        <div class="meta">Delivery Status: {{ ucfirst($status) }}</div>
    @endif

    @if(!empty($paymentStatus))
        <div class="meta">Payment Status: {{ ucfirst($paymentStatus) }}</div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Order #</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Phone</th>
                <th>Items</th>
                <th>Method</th>
                <th>Payment</th>
                <th>Delivery</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->created_at->format('M d, Y') }}</td>
                    <td>{{ $order->customer_name }}</td>
                    <td>{{ $order->customer_phone }}</td>
                    <td>{{ $order->items->count() }}</td>
                    <td>{{ strtoupper($order->payment_method) }}</td>
                    <td>{{ $order->payment_method === 'esewa' ? 'paid' : $order->payment_status }}</td>
                    <td>{{ $order->delivery_status }}</td>
                    <td class="text-right">{{ number_format((float) $order->total_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="8"><strong>Total</strong></td>
                <td class="text-right"><strong>{{ number_format((float) $orders->sum('total_amount'), 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 30px; font-size: 12px; color: #666;">
        Generated on: {{ now()->format('M d, Y H:i') }}
    </div>
</body>
</html>
