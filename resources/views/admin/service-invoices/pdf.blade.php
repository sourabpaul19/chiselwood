<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $serviceInvoice->invoice_no }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
            position: relative;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }

        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 6px; }
        .no-border td { border: none; }
        .header-table td { border: none; }

        .title { font-size: 18px; font-weight: bold; text-align: right; }
        .small { font-size: 11px; }

        .footer { margin-top: 30px; }

        /* Watermark */
        .watermark {
            position: absolute;
            top: 40%;
            left: 20%;
            font-size: 80px;
            color: rgba(255,0,0,0.2);
            transform: rotate(-45deg);
            z-index: 0;
            pointer-events: none;
        }
    </style>
</head>
<body>

@if($serviceInvoice->status === 'cancelled' || $serviceInvoice->is_canceled)
    <div class="watermark">CANCELLED</div>
@endif

@php
$states = [
    'AP'=>'Andhra Pradesh','AR'=>'Arunachal Pradesh','AS'=>'Assam','BR'=>'Bihar','CG'=>'Chhattisgarh',
    'GA'=>'Goa','GJ'=>'Gujarat','HR'=>'Haryana','HP'=>'Himachal Pradesh','JK'=>'Jammu & Kashmir',
    'JH'=>'Jharkhand','KA'=>'Karnataka','KL'=>'Kerala','MP'=>'Madhya Pradesh','MH'=>'Maharashtra',
    'MN'=>'Manipur','ML'=>'Meghalaya','MZ'=>'Mizoram','NL'=>'Nagaland','OD'=>'Odisha','PB'=>'Punjab',
    'RJ'=>'Rajasthan','SK'=>'Sikkim','TN'=>'Tamil Nadu','TG'=>'Telangana','TR'=>'Tripura',
    'UT'=>'Uttarakhand','UP'=>'Uttar Pradesh','WB'=>'West Bengal','AN'=>'Andaman & Nicobar Islands',
    'CH'=>'Chandigarh','DN'=>'Dadra & Nagar Haveli','DD'=>'Daman & Diu','DL'=>'Delhi','LD'=>'Lakshadweep',
    'PY'=>'Puducherry'
];

$companyStateCode = setting('company_state');
$companyStateName = $states[$companyStateCode] ?? 'Unknown State';
@endphp

{{-- ================= HEADER ================= --}}
<table class="header-table">
    <tr>
        <td>
            @php $logo = setting('logo'); @endphp
            @if($logo && file_exists(public_path('storage/' . $logo)))
                <img src="{{ public_path('storage/' . $logo) }}" style="height:30px; display:block; margin:0 auto;">
            @endif
        </td>
        <td class="text-right"><div class="title">TAX INVOICE</div></td>
    </tr>
    <tr>
        <td width="60%">
            <span class="bold">{{ setting('company_name') }}</span><br>
            {{ setting('company_address') }}<br>
        </td>
        <td width="40%" class="text-right">
            GSTIN: <b>{{ setting('gstin') }}</b><br>
            State: {{ $companyStateName }}
        </td>
    </tr>
</table>

<hr>

{{-- ================= CLIENT & INVOICE INFO ================= --}}
<table class="no-border">
    <tr>
        <td width="50%">
            <b>Bill To:</b><br>
            <strong>{{ $serviceInvoice->client->company_name ?? $serviceInvoice->client->user->name }}</strong><br>
            {{ $serviceInvoice->client->address ?? '' }}<br>
            GSTIN: {{ $serviceInvoice->client->gstin ?? 'Unregistered' }}<br>
            State: {{ $states[$serviceInvoice->client->client_state ?? ''] ?? 'Unknown State' }}
        </td>
        <td width="50%">
            <table>
                <tr><td colspan="2"><b>Invoice Details:</b></td></tr>
                <tr><td>Invoice No</td><td>{{ $serviceInvoice->invoice_no }}</td></tr>
                <tr><td>Invoice Date</td><td>{{ \Carbon\Carbon::parse($serviceInvoice->invoice_date)->format('d-m-Y') }}</td></tr>
                <tr><td>Place of Supply</td><td>{{ $states[$serviceInvoice->client->client_state ?? ''] ?? 'Unknown State' }}</td></tr>
                <tr><td>Reverse Charge</td><td>No</td></tr>
            </table>
        </td>
    </tr>
</table>

<br>

{{-- ================= ITEMS TABLE ================= --}}
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Service</th>
            <th>Qty</th>
            <th class="text-right">Rate</th>
            <th class="text-right">Discount</th>
            <th class="text-right">Taxable Value</th>
            <th class="text-center">GST %</th>
            <th class="text-right">CGST</th>
            <th class="text-right">SGST</th>
            <th class="text-right">IGST</th>
            <th class="text-right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($serviceInvoice->items as $i => $item)
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td>{{ $item->service_name }}</td>
            <td class="text-center">{{ $item->units ?? 1 }}</td>
            <td class="text-right">{{ number_format($item->unit_price,2) }}</td>
            <td class="text-right">
                @if($item->discount_type === 'percent')
                    {{ $item->discount_value }}%
                @else
                    ₹ {{ number_format($item->discount_value,2) }}
                @endif
            </td>
            <td class="text-right">{{ number_format($item->taxable_amount,2) }}</td>
            <td class="text-center">{{ $item->gst_rate }}%</td>
            <td class="text-right">{{ number_format($item->cgst,2) }}</td>
            <td class="text-right">{{ number_format($item->sgst,2) }}</td>
            <td class="text-right">{{ number_format($item->igst,2) }}</td>
            <td class="text-right">{{ number_format($item->total_price,2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<br>

{{-- ================= TOTALS ================= --}}
<table>
    <tr>
        <td width="70%">
            <b>Amount in Words:</b><br>
            {{ \Illuminate\Support\Str::ucfirst(\App\Helpers\NumberToWords::convert($serviceInvoice->grand_total)) }} Only
        </td>
        <td width="30%">
            <table>
                <tr><td>Taxable Amount</td><td class="text-right">{{ number_format($serviceInvoice->taxable_amount,2) }}</td></tr>
                <tr><td>CGST</td><td class="text-right">{{ number_format($serviceInvoice->cgst,2) }}</td></tr>
                <tr><td>SGST</td><td class="text-right">{{ number_format($serviceInvoice->sgst,2) }}</td></tr>
                <tr><td>IGST</td><td class="text-right">{{ number_format($serviceInvoice->igst,2) }}</td></tr>
                <tr><td>Invoice Discount</td><td class="text-right">{{ number_format($serviceInvoice->discount,2) }}</td></tr>
                <tr><td class="bold">Grand Total</td><td class="bold text-right">{{ number_format($serviceInvoice->grand_total,2) }}</td></tr>
            </table>
        </td>
    </tr>
</table>

<br>

{{-- ================= GST SUMMARY ================= --}}
<table>
    <thead>
        <tr>
            <th>GST %</th>
            <th>Taxable</th>
            <th>CGST</th>
            <th>SGST</th>
            <th>IGST</th>
        </tr>
    </thead>
    <tbody>
        @php $grouped = $serviceInvoice->items->groupBy('gst_rate'); @endphp
        @foreach($grouped as $rate => $rows)
        <tr>
            <td class="text-center">{{ $rate }}%</td>
            <td class="text-right">{{ number_format($rows->sum('taxable_amount'),2) }}</td>
            <td class="text-right">{{ number_format($rows->sum('cgst'),2) }}</td>
            <td class="text-right">{{ number_format($rows->sum('sgst'),2) }}</td>
            <td class="text-right">{{ number_format($rows->sum('igst'),2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- ================= FOOTER ================= --}}
<div class="footer">
    <p class="small">{{ setting('invoice_footer') }}</p>
    <table class="no-border">
        <tr>
            <td width="60%"></td>
            <td width="40%" class="text-center">
                For <b>{{ setting('company_name') }}</b><br><br><br>
                Authorised Signatory
            </td>
        </tr>
    </table>
</div>

</body>
</html>