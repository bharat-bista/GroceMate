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
        table { mso-displayed-decimal-separator:"\\."; mso-displayed-thousand-separator:"\\,"; }
        .number { mso-number-format:0\.00; text-align:right; }
        .text { mso-number-format:\@; }
    </style>
</head>
<body>
    @if($from && $to)
        <p><strong>Date Range:</strong> {{ \Carbon\Carbon::parse($from)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($to)->format('M d, Y') }}</p>
    @endif

    <table border="1" cellspacing="0" cellpadding="0">
        <thead>
            <tr>
                <th>Date</th>
                <th>Order #</th>
                <th>Customer</th>
                <th>Business</th>
                <th>Units</th>
                <th>Gross Income</th>
                <th>Estimated Profit</th>
                <th>Method</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incomes as $income)
                <tr>
                    <td class="text">{{ \Carbon\Carbon::parse($income->order_created_at)->format('Y-m-d') }}</td>
                    <td class="text">{{ $income->order_number }}</td>
                    <td class="text">{{ $income->customer_name }}</td>
                    <td class="text">{{ $income->business_name ?? 'Unassigned' }}</td>
                    <td class="number">{{ (int) $income->total_units }}</td>
                    <td class="number">{{ (float) $income->business_gross_income }}</td>
                    <td class="number">{{ (float) $income->estimated_profit }}</td>
                    <td class="text">{{ strtoupper((string) $income->payment_method) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5"><strong>Total</strong></td>
                <td class="number"><strong>{{ (float) $incomes->sum('business_gross_income') }}</strong></td>
                <td class="number"><strong>{{ (float) $incomes->sum('estimated_profit') }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
