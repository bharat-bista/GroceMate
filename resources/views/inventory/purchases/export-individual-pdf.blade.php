<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Report of ID{{ $purchase->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        .header-info { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .header-info div { margin-bottom: 5px; }
        .header-info strong { color: #555; }
        .debug-info { background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; margin-bottom: 20px; border-radius: 5px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #f9f9f9; }
        .footer { margin-top: 30px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    
    
    <h1>Purchase Report of ID:{{ $purchase->id }}</h1>
    <h1>Supplier ID:{{ $purchase->supplier_id }}</h1>
    <div class="header-info">
        <div><strong>Date:</strong> {{ $purchase->purchase_date->setTimezone('Asia/Kathmandu')->format('M d, Y') }}</div>
        <div><strong>Supplier:</strong> {{ $purchase->supplier->name ?? 'N/A' }}</div>
        <div><strong>Invoice No:</strong> {{ $purchase->invoice_no ?? 'N/A' }}</div>
        <div><strong>Created By:</strong> {{ $purchase->creator->name ?? 'N/A' }}</div>
        <div><strong>Total Cost:</strong> Rs {{ number_format($purchase->total_cost, 2) }}</div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Unit Cost</th>
                <th class="text-right">Tax</th>
                <th class="text-right">Line Total</th>
                <th>Expiry Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($purchase->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-right">{{ $item->qty }}</td>
                    <td class="text-right">{{ number_format($item->unit_cost, 2) }}</td>
                    <td class="text-right">{{ number_format($item->taxes->sum('pivot.tax_amount'), 2) }}</td>
                    <td class="text-right">{{ number_format($item->line_total, 2) }}</td>
                    <td>{{ $item->expiry_date?->format('Y-m-d') ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4"><strong>Total</strong></td>
                <td class="text-right"><strong>Rs {{ number_format($purchase->total_cost, 2) }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    
    <div class="footer">
        Generated on: {{ \Carbon\Carbon::now('Asia/Kathmandu')->format('F j, Y') }} at {{ \Carbon\Carbon::now('Asia/Kathmandu')->format('g:i A') }} (NPT)<br>
        Purchase ID: #{{ $purchase->id }}
    </div>
</body>
</html>
