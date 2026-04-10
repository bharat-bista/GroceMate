<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->invoice_no }}</title>
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
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .invoice-details {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .total {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Invoice #{{ $invoice->invoice_no }}</h1>
        <p>Thank you for your business!</p>
    </div>

    <div class="invoice-details">
        <h3>Invoice Details</h3>
        <p><strong>Invoice Number:</strong> {{ $invoice->invoice_no }}</p>
        <p><strong>Date:</strong> {{ $invoice->invoice_date->format('d M Y') }}</p>
        <p><strong>Business:</strong> {{ $invoice->business->business_name }}</p>
        <p><strong>Payment Method:</strong> {{ ucfirst($invoice->payment_method) }}</p>
        
        <h3>Customer Information</h3>
        <p><strong>Name:</strong> {{ $invoice->customer->name }}</p>
        @if($invoice->customer->email)
        <p><strong>Email:</strong> {{ $invoice->customer->email }}</p>
        @endif
        @if($invoice->customer->phone)
        <p><strong>Phone:</strong> {{ $invoice->customer->phone }}</p>
        @endif
        @if($invoice->customer->address)
        <p><strong>Address:</strong> {{ $invoice->customer->address }}</p>
        @endif
        
        <h3>Order Summary</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Product</th>
                    <th style="padding: 10px; text-align: center; border: 1px solid #dee2e6;">Qty</th>
                    <th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Price</th>
                    <th style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $item->product_name }}</td>
                    <td style="padding: 10px; text-align: center; border: 1px solid #dee2e6;">{{ $item->qty }} {{ $item->unit }}</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Rs {{ number_format($item->unit_cost, 2) }}</td>
                    <td style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Rs {{ number_format($item->line_total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Total Amount:</th>
                    <th class="total" style="padding: 10px; text-align: right; border: 1px solid #dee2e6;">Rs {{ number_format($invoice->total_cost, 2) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer">
        <p>This is an automated message. Please find your detailed invoice attached as a PDF.</p>
        <p>If you have any questions, please contact us.</p>
    </div>
</body>
</html>
