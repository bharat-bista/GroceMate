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
                <th>Supplier</th>
                <th>Business Account</th>
                <th>Payment Method</th>
                <th>Payment Type</th>
                <th>Amount</th>
                <th>Bank Charge</th>
                <th>TDS Applicable</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td class="text">{{ \Carbon\Carbon::parse($payment->date)->format('Y-m-d') }}</td>
                    <td class="text">PAY-{{ str_pad($payment->id, 4, '0', STR_PAD_LEFT) }}</td>
                    <td class="text">{{ $payment->supplier->name ?? 'N/A' }}</td>
                    <td class="text">{{ $payment->business_account ?? 'N/A' }}</td>
                    <td class="text">{{ ucfirst($payment->payment_method) }}</td>
                    <td class="text">{{ ucfirst($payment->payment_type ?? 'external') }}</td>
                    <td class="number">{{ $payment->amount }}</td>
                    <td class="number">{{ $payment->bank_charge }}</td>
                    <td class="text">{{ $payment->tds_applicable ? 'Yes' : 'No' }}</td>
                    <td class="text">{{ $payment->note ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6"><strong>Total</strong></td>
                <td class="number"><strong>{{ $payments->sum('amount') }}</strong></td>
                <td class="number"><strong>{{ $payments->sum('bank_charge') }}</strong></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
