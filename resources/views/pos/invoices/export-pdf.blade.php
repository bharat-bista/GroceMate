<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoices Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        .date-range { color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
    </style>
</head>
<body>
    <h1>Invoices Report</h1>
    
    @if($from && $to)
        <div class="date-range">
            Date Range: {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}
        </div>
    @endif
    
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Business</th>
                <th>Customer</th>
                <th>Invoice No</th>
                <th class="text-right">Total</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                    <td>{{ $invoice->business->business_name ?? 'N/A' }}</td>
                    <td>{{ $invoice->customer->name ?? 'N/A' }}</td>
                    <td>{{ $invoice->invoice_no ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($invoice->total_cost, 2) }}</td>
                    <td>{{ $invoice->creator->name ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4"><strong>Total</strong></td>
                <td class="text-right"><strong>{{ number_format($invoices->sum('total_cost'), 2) }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    
    <div style="margin-top: 30px; font-size: 12px; color: #666;">
        Generated on: {{ now()->format('M d, Y H:i') }}
    </div>
</body>
</html>
