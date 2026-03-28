<table border="1">
    <tr>
        <th colspan="6" style="background:#dbeafe;font-size:16px;">{{ $business->business_name }} Report</th>
    </tr>
    <tr>
        <td><strong>Business Type</strong></td>
        <td>{{ $business->business_type ?: 'N/A' }}</td>
        <td><strong>Owner</strong></td>
        <td>{{ $business->owner_name ?: 'N/A' }}</td>
        <td><strong>Range</strong></td>
        <td>{{ $from ?: 'All Time' }} to {{ $to ?: 'All Time' }}</td>
    </tr>
</table>

<br>

<table border="1">
    <tr>
        <th style="background:#f1f5f9;">Metric</th>
        <th style="background:#f1f5f9;">Amount</th>
    </tr>
    <tr><td>Total Sales</td><td>Rs {{ number_format($salesTotal, 2) }}</td></tr>
    <tr><td>Total Purchases</td><td>Rs {{ number_format($purchaseTotal, 2) }}</td></tr>
    <tr><td>Income Received</td><td>Rs {{ number_format($incomeTotal, 2) }}</td></tr>
    <tr><td>Supplier Payments</td><td>Rs {{ number_format($supplierPaymentTotal, 2) }}</td></tr>
    <tr><td>Net Cash Flow</td><td>Rs {{ number_format($netCashFlow, 2) }}</td></tr>
    <tr><td>Current Balance</td><td>Rs {{ number_format((float) $business->balance, 2) }}</td></tr>
</table>

<br>

<table border="1">
    <thead>
        <tr>
            <th style="background:#e2e8f0;">Date</th>
            <th style="background:#e2e8f0;">Type</th>
            <th style="background:#e2e8f0;">Reference</th>
            <th style="background:#e2e8f0;">Party</th>
            <th style="background:#e2e8f0;">Direction</th>
            <th style="background:#e2e8f0;">Amount</th>
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
                <td>Rs {{ number_format($activity['amount'], 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6">No transactions found for this business account.</td>
            </tr>
        @endforelse
    </tbody>
</table>
