<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Ecommerce Income Report</title>
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
    <h1>Ecommerce Income Report</h1>

    @if($from && $to)
        <div class="date-range">
            Date Range: {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}
        </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Order #</th>
                <th>Customer</th>
                <th>Business</th>
                <th class="text-right">Units</th>
                <th class="text-right">Gross Income</th>
                <th class="text-right">Estimated Profit</th>
                <th>Method</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incomes as $income)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($income->order_created_at)->format('Y-m-d') }}</td>
                    <td>{{ $income->order_number }}</td>
                    <td>{{ $income->customer_name }}</td>
                    <td>{{ $income->business_name ?? 'Unassigned' }}</td>
                    <td class="text-right">{{ (int) $income->total_units }}</td>
                    <td class="text-right">{{ number_format((float) $income->business_gross_income, 2) }}</td>
                    <td class="text-right">{{ number_format((float) $income->estimated_profit, 2) }}</td>
                    <td>{{ strtoupper((string) $income->payment_method) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5"><strong>Total</strong></td>
                <td class="text-right"><strong>{{ number_format((float) $incomes->sum('business_gross_income'), 2) }}</strong></td>
                <td class="text-right"><strong>{{ number_format((float) $incomes->sum('estimated_profit'), 2) }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
