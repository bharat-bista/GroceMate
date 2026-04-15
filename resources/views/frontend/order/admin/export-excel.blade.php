<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta charset="utf-8">
    <meta name=ProgId content=Excel.Sheet>
    <meta name=Generator content="Microsoft Excel">
    <!--[if gte mso 9]><xml>
     <o:OfficeDocumentSettings>
      <o:PixelsPerInch>96</o:PixelsPerInch>
     </o:OfficeDocumentSettings>
    </xml><![endif]-->
    <style>
        table { mso-displayed-decimal-separator:"\\."; mso-displayed-thousand-separator:"\\,"; }
        .number { mso-number-format:0\.00; text-align:right; }
        .text { mso-number-format:\@; }
    </style>
</head>
<body>
    @if($from && $to)
        <p><strong>Date Range:</strong> {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</p>
    @endif

    @if(!empty($search))
        <p><strong>Search:</strong> {{ $search }}</p>
    @endif

    @if(!empty($status))
        <p><strong>Delivery Status:</strong> {{ ucfirst($status) }}</p>
    @endif

    @if(!empty($paymentStatus))
        <p><strong>Payment Status:</strong> {{ ucfirst($paymentStatus) }}</p>
    @endif

    <table border="1" cellspacing="0" cellpadding="0">
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
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td class="text">{{ $order->order_number }}</td>
                    <td class="text">{{ $order->created_at->format('Y-m-d') }}</td>
                    <td class="text">{{ $order->customer_name }}</td>
                    <td class="text">{{ $order->customer_phone }}</td>
                    <td class="number">{{ $order->items->count() }}</td>
                    <td class="text">{{ strtoupper($order->payment_method) }}</td>
                    <td class="text">{{ $order->payment_method === 'esewa' ? 'paid' : $order->payment_status }}</td>
                    <td class="text">{{ $order->delivery_status }}</td>
                    <td class="number">{{ (float) $order->total_amount }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="8"><strong>Total</strong></td>
                <td class="number"><strong>{{ (float) $orders->sum('total_amount') }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
