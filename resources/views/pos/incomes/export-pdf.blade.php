<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Income Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; margin-bottom: 20px; }
        .date-range { color: #666; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .income { color: #059669; }
        .expense { color: #dc2626; }
    </style>
</head>
<body>
    <h1>Income Report</h1>
    
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
                <th>Customer</th>
                <th>Business</th>
                <th>Payment Method</th>
                <th>Type</th>
                <th class="text-right">Amount</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incomes as $income)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($income->transaction_date ?? $income->created_at)->format('M d, Y') }}</td>
                    <td>{{ $income->reference_no ?? 'INC-' . str_pad($income->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $income->customer->name ?? 'N/A' }}</td>
                    <td>{{ $income->business->business_name ?? 'N/A' }}</td>
                    <td>{{ ucfirst($income->payment_method) }}</td>
                    <td>{{ $income->income_type ?? 'Other' }}</td>
                    <td class="text-right {{ $income->amount_received > 0 ? 'income' : 'expense' }}">
                        {{ number_format($income->amount_received, 2) }}
                    </td>
                    <td>{{ $income->notes ?? $income->description ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6"><strong>Total</strong></td>
                <td class="text-right"><strong>{{ number_format($incomes->sum('amount_received'), 2) }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    
    <div style="margin-top: 30px; font-size: 12px; color: #666;">
        Generated on: {{ now()->format('M d, Y H:i') }}
    </div>
</body>
</html>
