<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Business Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; font-size: 12px; }
        h1, h2 { margin: 0 0 8px; }
        .meta { margin-bottom: 18px; }
        .meta p { margin: 3px 0; }
        .summary { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .summary td { border: 1px solid #d1d5db; padding: 8px 10px; }
        .summary .label { background: #f8fafc; font-weight: bold; width: 25%; }
        table.report { width: 100%; border-collapse: collapse; }
        table.report th, table.report td { border: 1px solid #d1d5db; padding: 8px 10px; text-align: left; }
        table.report th { background: #e2e8f0; font-weight: bold; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h1>{{ $business->business_name }} Report</h1>
    <div class="meta">
        <p><strong>Business Type:</strong> {{ $business->business_type ?: 'N/A' }}</p>
        <p><strong>Owner:</strong> {{ $business->owner_name ?: 'N/A' }}</p>
        <p><strong>Report Range:</strong> {{ $from ?: 'All Time' }} to {{ $to ?: 'All Time' }}</p>
    </div>

    <table class="summary">
        <tr>
            <td class="label">Total Sales</td>
            <td>Rs {{ number_format($salesTotal, 2) }}</td>
            <td class="label">Total Purchases</td>
            <td>Rs {{ number_format($purchaseTotal, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Income Received</td>
            <td>Rs {{ number_format($incomeTotal, 2) }}</td>
            <td class="label">Supplier Payments</td>
            <td>Rs {{ number_format($supplierPaymentTotal, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Net Cash Flow</td>
            <td>Rs {{ number_format($netCashFlow, 2) }}</td>
            <td class="label">Current Balance</td>
            <td>Rs {{ number_format((float) $business->balance, 2) }}</td>
        </tr>
    </table>

    <h2>Account Activity</h2>
    <table class="report">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Reference</th>
                <th>Party</th>
                <th>Direction</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activityFeed as $activity)
                <tr>
                    <td>{{ $activity['date']->format('Y-m-d') }}</td>
                    <td>{{ $activity['type_label'] }}</td>
                    <td>{{ $activity['reference'] }}</td>
                    <td>{{ $activity['party'] }}</td>
                    <td>{{ $activity['direction'] }}</td>
                    <td class="text-right">Rs {{ number_format($activity['amount'], 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No transactions found for this business account.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
