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
    </style>
</head>
<body>
    <table border="1" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th>Date</th>
                <th>Business</th>
                <th>Customer</th>
                <th>Invoice No</th>
                <th>Total</th>
                <th>Created By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoices as $invoice)
                <tr>
                    <td class="text">{{ $invoice->invoice_date->format('Y-m-d') }}</td>
                    <td class="text">{{ $invoice->business->business_name ?? 'N/A' }}</td>
                    <td class="text">{{ $invoice->customer->name ?? 'N/A' }}</td>
                    <td class="text">{{ $invoice->invoice_no ?? 'N/A' }}</td>
                    <td class="number">{{ $invoice->total_cost }}</td>
                    <td class="text">{{ $invoice->creator->name ?? 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4"><strong>Total</strong></td>
                <td class="number"><strong>{{ $invoices->sum('total_cost') }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
