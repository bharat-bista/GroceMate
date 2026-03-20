<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Supplier Payments Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        .date-range { color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .expense { color: #dc2626; }
    </style>
</head>
<body>
    <h1>Supplier Payments Report</h1>
    
    @if($from && $to)
        <div class="date-range">
            Date Range: {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}
        </div>
    @endif
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Reference</th>
                <th>Supplier</th>
                <th>Business Account</th>
                <th>Payment Method</th>
                <th>Payment Type</th>
                <th class="text-right">Amount</th>
                <th>Bank Charge</th>
                <th>TDS</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($payment->date)->format('M d, Y') }}</td>
                    <td>PAY-{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $payment->supplier->name ?? 'N/A' }}</td>
                    <td>{{ $payment->business_account ?? 'N/A' }}</td>
                    <td>{{ ucfirst($payment->payment_method) }}</td>
                    <td>{{ ucfirst($payment->payment_type ?? 'external') }}</td>
                    <td class="text-right expense">Rs {{ number_format($payment->amount, 2) }}</td>
                    <td>Rs {{ number_format($payment->bank_charge, 2) }}</td>
                    <td>{{ $payment->tds_applicable ? 'Yes' : 'No' }}</td>
                    <td>{{ $payment->note ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6"><strong>Total Amount</strong></td>
                <td class="text-right"><strong>Rs {{ number_format($payments->sum('amount'), 2) }}</strong></td>
                <td><strong>Rs {{ number_format($payments->sum('bank_charge'), 2) }}</strong></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    
    <div style="margin-top: 30px; font-size: 12px; color: #666;">
        Generated on: {{ now()->format('M d, Y H:i') }}
    </div>
</body>
</html>
