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
        table { mso-displayed-decimal-separator:"\."; mso-displayed-thousand-separator:"\,"; }
        .number { mso-number-format:0\.00; text-align:right; }
        .text { mso-number-format:\@; }
        .header { font-weight:bold; background-color:#f2f2f2; }
    </style>
</head>
<body>
    <table border="1" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th colspan="6" class="header">Invoice Report #{{ $invoice->id }}</th>
            </tr>
            <tr>
                <th colspan="2" class="header">Date: {{ $invoice->invoice_date->format('Y-m-d') }}</th>
                <th colspan="2" class="header">Business: {{ $invoice->business->business_name ?? 'N/A' }}</th>
                <th colspan="2" class="header">Customer: {{ $invoice->customer->name ?? 'N/A' }}</th>
            </tr>
            <tr class="header">
                <th>Product</th>
                <th>Quantity</th>
                <th>Unit Cost</th>
                <th>Tax</th>
                <th>Line Total</th>
                <th>Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td class="text">{{ $item->product_name }}</td>
                    <td class="number">{{ $item->qty }}</td>
                    <td class="number">{{ $item->unit_cost }}</td>
                    <td class="number">{{ $item->tax_total }}</td>
                    <td class="number">{{ $item->line_total }}</td>
                    <td class="text">{{ $item->unit ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"><strong>Total</strong></td>
                <td class="number"><strong>{{ $invoice->total_cost }}</strong></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="6" class="text">
                    Invoice No: {{ $invoice->invoice_no ?? 'N/A' }} | 
                    Created By: {{ $invoice->creator->name ?? 'N/A' }} | 
                    Generated: {{ now()->format('Y-m-d H:i') }}
                </td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
