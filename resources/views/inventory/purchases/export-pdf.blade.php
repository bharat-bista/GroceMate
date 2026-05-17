<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchases Report</title>
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
    <h1>Purchases Report</h1>
    @if(!empty($businessName))
        <div class="date-range">Business: {{ $businessName }}</div>
    @endif
    
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
                <th>Supplier</th>
                <th>Invoice No</th>
                <th class="text-right">Total</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchases as $purchase)
                <tr>
                    <td>{{ $purchase->purchase_date->format('M d, Y') }}</td>
                    <td>{{ $purchase->business->business_name ?? 'N/A' }}</td>
                    <td>{{ $purchase->supplier->name ?? 'N/A' }}</td>
                    <td>{{ $purchase->invoice_no ?? 'N/A' }}</td>
                    <td class="text-right">{{ number_format($purchase->total_cost, 0) }}</td>
                    <td>{{ $purchase->creator->name ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4"><strong>Total</strong></td>
                <td class="text-right"><strong>{{ number_format($purchases->sum('total_cost'), 0) }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    
    <div style="margin-top: 30px; font-size: 12px; color: #666;">
        Generated on: {{ now()->format('M d, Y H:i') }}
    </div>
</body>
</html>
