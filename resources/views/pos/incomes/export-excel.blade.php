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
                <th>Reference</th>
                <th>Customer</th>
                <th>Business</th>
                <th>Payment Method</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incomes as $income)
                <tr>
                    <td class="text">{{ \Carbon\Carbon::parse($income->transaction_date ?? $income->amount_received)->format('Y-m-d') }}</td>
                    <td class="text">{{ $income->reference_no ?? 'INC-' . str_pad($income->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="text">{{ $income->customer->name ?? 'N/A' }}</td>
                    <td class="text">{{ $income->business->business_name ?? 'N/A' }}</td>
                    <td class="text">{{ ucfirst($income->payment_method) }}</td>
                    <td class="text">{{ $income->income_type ?? 'Other' }}</td>
                    <td class="number">{{ $income->amount_received }}</td>
                    <td class="text">{{ $income->notes ?? $income->description ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6"><strong>Total</strong></td>
                <td class="number"><strong>{{ $incomes->sum('amount_received') }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
