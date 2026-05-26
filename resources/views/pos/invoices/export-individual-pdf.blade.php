<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Tax Invoice</title>

<style>
@page { size: A4; margin: 10mm; }

body {
    font-family: "Times New Roman", serif;
    font-size: 13px;
    margin: 0;
}

table {
    border-collapse: collapse;
    width: 100%;
}

th, td {
    border: 1px solid #000;
    border-collapse: collapse;
    padding: 5px;
    vertical-align: top;
}

th {
    text-align: center;
    font-weight: bold;
}

/* Column widths for bill format */
th:nth-child(1) { width: 8%; }  /* SN */
th:nth-child(2) { width: 40%; } /* Description of goods - more space */
th:nth-child(3) { width: 10%; } /* Qty - less space */
th:nth-child(4) { width: 12%; } /* Unit */
th:nth-child(5) { width: 15%; } /* Price */
th:nth-child(6) { width: 15%; } /* Amount */

.center { text-align: center; }
.right { text-align: right; }

.store-name {
    font-size: 20px;
    font-weight: bold;
}
</style>
</head>

<body>

@php
$subTotal   = (float) $invoice->items->sum('line_total');
$discount   = (int) ($invoice->discount ?? 0);
$grand      = (float) $invoice->total_cost;  // authoritative: base + tax − discount
$tax        = max(0, (int) round($grand - $subTotal + $discount));
$totalUnits = $invoice->items->sum('qty');

$mittiDate = '';
try {
    $mittiDate = \Anuzpandey\LaravelNepaliDate\LaravelNepaliDate::from(
        $invoice->invoice_date->format('Y-m-d')
    )->toNepaliDate('Y-m-d');
} catch (\Exception $e) {}

function numberToWords($num){
    $ones = ['', 'One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten',
    'Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
    $tens = ['', '', 'Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];

    if ($num == 0) return 'Zero';
    $str='';

    if ($num >= 10000000){
        $str .= numberToWords(intval($num/10000000)).' Crore ';
        $num %= 10000000;
    }
    if ($num >= 100000){
        $str .= numberToWords(intval($num/100000)).' Lakh ';
        $num %= 100000;
    }
    if ($num >= 1000){
        $str .= numberToWords(intval($num/1000)).' Thousand ';
        $num %= 1000;
    }
    if ($num >= 100){
        $str .= numberToWords(intval($num/100)).' Hundred ';
        $num %= 100;
    }
    if ($num > 0){
        if ($num < 20) $str .= $ones[$num];
        else $str .= $tens[intval($num/10)].' '.$ones[$num%10];
    }
    return trim($str);
}

$int = floor($grand);
$paisa = round(($grand-$int)*100);
$words = numberToWords($int);
if($paisa>0) $words .= " and Paisa ".numberToWords($paisa);
$words .= " Only";
@endphp


<table>

<!-- HEADER -->
<tr>
<td colspan="6" class="center">
    <strong>&lt;&lt; TAX INVOICE &gt;&gt;</strong><br>
    <span class="store-name">{{ $invoice->business->business_name }}</span><br>
    {{ $invoice->business->address }}<br>
    VAT No : {{ $invoice->business->vat_no }}
</td>
</tr>

<!-- CUSTOMER + INVOICE -->
<tr>
<td colspan="3" style="border-right: none;">
    <strong>Customer Name :</strong> {{ $invoice->customer->name }}<br>
    <strong>Address :</strong> {{ $invoice->customer->address }}<br>
    <strong>Customer VAT No :</strong> {{ $invoice->customer->vat_number }}<br>
    <strong>Contact No :</strong> {{ $invoice->customer->phone }}<br>
    <strong>Mode of payment :</strong> {{ ucfirst($invoice->payment_method) }}
</td>

<td colspan="3" style="border-left: none;">
    <strong>Invoice No :</strong> {{ $invoice->invoice_no }}<br>
    <strong>Mitti Date :</strong> {{ $mittiDate }}<br>
    <strong>Roman Date :</strong> {{ optional($invoice->created_at)->format('d/m/Y') }}
</td>
</tr>

<!-- TABLE HEAD -->
<tr>
<th>SN.</th>
<th>Description of goods</th>
<th>Qty.</th>
<th>Unit</th>
<th>Price</th>
<th>Amount (Rs.)</th>
</tr>

<!-- ITEMS -->
@php 
    $sn = 1;
    $max_item_rows = 15; // Fixed number of rows for bill format
    $item_count = count($invoice->items);
@endphp
@foreach($invoice->items as $item)
<tr>
<td class="center" style="border-top: none; border-bottom: none;">{{ $sn++ }}</td>
<td style="border-top: none; border-bottom: none;">{{ $item->description ?? $item->product_name }}</td>
<td class="right" style="border-top: none; border-bottom: none;">{{ number_format($item->qty, 0) }}</td>
<td class="center" style="border-top: none; border-bottom: none;">{{ $item->unit }}</td>
<td class="right" style="border-top: none; border-bottom: none;">{{ number_format($item->price ?? $item->unit_cost, 0) }}</td>
<td class="right" style="border-top: none; border-bottom: none;">
{{ number_format($item->line_total ?? ($item->qty*($item->price ?? $item->unit_cost)), 0) }}
</td>
</tr>
@endforeach

<!-- EMPTY ROWS FOR FIXED BILL FORMAT -->
@if ($item_count < $max_item_rows)
    @for ($i = 0; $i < ($max_item_rows - $item_count); $i++)
    <tr>
    <td class="center" style="border-top: none; border-bottom: none;">&nbsp;</td>
    <td style="border-top: none; border-bottom: none;">&nbsp;</td>
    <td class="right" style="border-top: none; border-bottom: none;">&nbsp;</td>
    <td class="center" style="border-top: none; border-bottom: none;">&nbsp;</td>
    <td class="right" style="border-top: none; border-bottom: none;">&nbsp;</td>
    <td class="right" style="border-top: none; border-bottom: none;">&nbsp;</td>
    </tr>
    @endfor
@endif

<!-- TOTAL -->
<tr>
<td colspan="3"><strong>{{ number_format($totalUnits, 0) }} Units</strong></td>
<td colspan="2" class="right"><strong>Total</strong></td>
<td class="right"><strong>{{ number_format($subTotal, 0) }}</strong></td>
</tr>

@if($tax > 0)
<tr>
<td colspan="4" style="border-top: none; border-bottom: none;"></td>
<td class="right" style="border-top: none; border-bottom: none;">Taxable Amount</td>
<td class="right" style="border-top: none; border-bottom: none;">{{ number_format($subTotal, 0) }}</td>
</tr>

<tr>
<td colspan="4" style="border-top: none; border-bottom: none;"></td>
<td class="right" style="border-top: none; border-bottom: none;">VAT 13%</td>
<td class="right" style="border-top: none; border-bottom: none;">{{ number_format($tax, 0) }}</td>
</tr>
@endif

@if($discount > 0)
<tr>
<td colspan="4" style="border-top: none; border-bottom: none;"></td>
<td class="right" style="border-top: none; border-bottom: none;">Discount</td>
<td class="right" style="border-top: none; border-bottom: none;">- {{ number_format($discount, 0) }}</td>
</tr>
@endif
<tr>
<td colspan="4" style="border-top: none; border-bottom: none;"></td>
<td class="right" style="border-top: none; border-bottom: none;"><strong>Grand Total</strong></td>
<td class="right" style="border-top: none; border-bottom: none;"><strong>{{ number_format($grand, 0) }}</strong></td>
</tr>

<!-- AMOUNT IN WORDS -->
<tr>
<td colspan="6">
<strong>Amount in words :</strong> {{ $words }}
</td>
</tr>

<!-- CONDITIONS -->
<tr>
<td colspan="3" style="border: 1px solid #000;">
<strong>Conditions</strong><br>
Goods once sold will not be taken back.<br>
Interest @ 18% p.a. will be charged if payment not made within stipulated time.
</td>

<td colspan="3" style="border: 1px solid #000;">
Receiver's Signature :
<hr style="border: 1px solid #000; margin: 10px 0;">
{{ $invoice->business->business_name }}<br>
<div style="height: 50px;"></div>
</td>
</tr>

<!-- AUTHORISED SIGNATORY FULL WIDTH -->
<tr>
<td colspan="6" class="center" style="height: 40px; padding-top: 15px;">
<strong>Authorised Signatory :</strong>
</td>
</tr>

</table>

</body>
</html>