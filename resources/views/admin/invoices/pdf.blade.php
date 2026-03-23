<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #000;
            padding: 6px;
        }

        .no-border td {
            border: none;
        }

        .header-table td {
            border: none;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
            text-align: right;
        }

        .small {
            font-size: 11px;
        }

        .footer {
            margin-top: 30px;
        }
        /* Watermark style */
        .watermark {
            position: absolute;
            top: 40%;
            left: 20%;
            font-size: 80px;
            color: rgba(255, 0, 0, 0.2); /* red with transparency */
            transform: rotate(-45deg);
            z-index: 0;
            pointer-events: none;
        }

        body {
            position: relative; /* Needed for absolute watermark */
        }
    </style>
</head>
<body>



@if($invoice->status === 'cancelled' || $invoice->is_canceled)
    <div class="watermark">CANCELLED</div>
@endif

@php
$states = [
    'AP' => 'Andhra Pradesh',
    'AR' => 'Arunachal Pradesh',
    'AS' => 'Assam',
    'BR' => 'Bihar',
    'CG' => 'Chhattisgarh',
    'GA' => 'Goa',
    'GJ' => 'Gujarat',
    'HR' => 'Haryana',
    'HP' => 'Himachal Pradesh',
    'JK' => 'Jammu & Kashmir',
    'JH' => 'Jharkhand',
    'KA' => 'Karnataka',
    'KL' => 'Kerala',
    'MP' => 'Madhya Pradesh',
    'MH' => 'Maharashtra',
    'MN' => 'Manipur',
    'ML' => 'Meghalaya',
    'MZ' => 'Mizoram',
    'NL' => 'Nagaland',
    'OD' => 'Odisha',
    'PB' => 'Punjab',
    'RJ' => 'Rajasthan',
    'SK' => 'Sikkim',
    'TN' => 'Tamil Nadu',
    'TG' => 'Telangana',
    'TR' => 'Tripura',
    'UT' => 'Uttarakhand',
    'UP' => 'Uttar Pradesh',
    'WB' => 'West Bengal',
    'AN' => 'Andaman & Nicobar Islands',
    'CH' => 'Chandigarh',
    'DN' => 'Dadra & Nagar Haveli',
    'DD' => 'Daman & Diu',
    'DL' => 'Delhi',
    'LD' => 'Lakshadweep',
    'PY' => 'Puducherry',
];

$companyStateCode = setting('company_state');
$companyStateName = $states[$companyStateCode] ?? 'Unknown State';
@endphp

@php
    $logo = setting('logo');
@endphp

{{-- ================= HEADER ================= --}}
<table class="header-table">
    <tr>
        <td>
            @if($logo && file_exists(public_path('storage/' . $logo)))
                <img src="{{ public_path('storage/' . $logo) }}" 
                     style="height: 30px;display: block;margin: 0 auto;">
            @endif
        </td>
        <td class="text-right">
            <div class="title">TAX INVOICE</div>
        </td>
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
            <strong>{{ $invoice->client->company_name }}</strong><br>
            {{ $invoice->client->user->name }}<br>
            {{ $invoice->client->address ?? '' }}<br>
            GSTIN: {{ $invoice->client->gstin ?? 'Unregistered' }}<br>
            State: {{
    [
        'AP'=>'Andhra Pradesh','AR'=>'Arunachal Pradesh','AS'=>'Assam','BR'=>'Bihar','CG'=>'Chhattisgarh',
        'GA'=>'Goa','GJ'=>'Gujarat','HR'=>'Haryana','HP'=>'Himachal Pradesh','JK'=>'Jammu & Kashmir',
        'JH'=>'Jharkhand','KA'=>'Karnataka','KL'=>'Kerala','MP'=>'Madhya Pradesh','MH'=>'Maharashtra',
        'MN'=>'Manipur','ML'=>'Meghalaya','MZ'=>'Mizoram','NL'=>'Nagaland','OD'=>'Odisha','PB'=>'Punjab',
        'RJ'=>'Rajasthan','SK'=>'Sikkim','TN'=>'Tamil Nadu','TG'=>'Telangana','TR'=>'Tripura',
        'UT'=>'Uttarakhand','UP'=>'Uttar Pradesh','WB'=>'West Bengal','AN'=>'Andaman & Nicobar Islands',
        'CH'=>'Chandigarh','DN'=>'Dadra & Nagar Haveli','DD'=>'Daman & Diu','DL'=>'Delhi','LD'=>'Lakshadweep',
        'PY'=>'Puducherry'
    ][$invoice->client->client_state ?? ''] ?? 'Unknown State'
}}
        </td>
        <td width="50%">
            <table>
                <tr>
                    <td colspan="2"><b>Invoice Details:</b></td>
                </tr>
                <tr>
                    <td>Invoice No</td>
                    <td>{{ $invoice->invoice_no }}</td>
                </tr>
                <tr>
                    <td>Invoice Date</td>
                    <td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td>Place of Supply</td>
                    <td>{{
    [
        'AP'=>'Andhra Pradesh','AR'=>'Arunachal Pradesh','AS'=>'Assam','BR'=>'Bihar','CG'=>'Chhattisgarh',
        'GA'=>'Goa','GJ'=>'Gujarat','HR'=>'Haryana','HP'=>'Himachal Pradesh','JK'=>'Jammu & Kashmir',
        'JH'=>'Jharkhand','KA'=>'Karnataka','KL'=>'Kerala','MP'=>'Madhya Pradesh','MH'=>'Maharashtra',
        'MN'=>'Manipur','ML'=>'Meghalaya','MZ'=>'Mizoram','NL'=>'Nagaland','OD'=>'Odisha','PB'=>'Punjab',
        'RJ'=>'Rajasthan','SK'=>'Sikkim','TN'=>'Tamil Nadu','TG'=>'Telangana','TR'=>'Tripura',
        'UT'=>'Uttarakhand','UP'=>'Uttar Pradesh','WB'=>'West Bengal','AN'=>'Andaman & Nicobar Islands',
        'CH'=>'Chandigarh','DN'=>'Dadra & Nagar Haveli','DD'=>'Daman & Diu','DL'=>'Delhi','LD'=>'Lakshadweep',
        'PY'=>'Puducherry'
    ][$invoice->client->client_state ?? ''] ?? 'Unknown State'
}}</td>
                </tr>
                <tr>
                    <td>Reverse Charge</td>
                    <td>No</td>
                </tr>
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
            <th>Item</th>
            <th>HSN</th>
            <th class="text-center">Qty</th>
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
        @foreach($invoice->items as $i => $row)
        <tr>
            <td class="text-center">{{ $i+1 }}</td>
            <td>{{ $row->item->name }}</td>
            <td>{{ $row->hsn ?? '-' }}</td>
            <td class="text-center">{{ $row->quantity }}</td>
            <td class="text-right">{{ number_format($row->unit_price,2) }}</td>
            <td class="text-right">
                @if($row->discount_type === 'percent')
                    {{ $row->discount_value }}%
                @else
                    ₹ {{ number_format($row->discount_value,2) }}
                @endif
            </td>
            <td class="text-right">{{ number_format($row->taxable_amount,2) }}</td>
            <td class="text-center">{{ $row->gst_rate }}%</td>
            <td class="text-right">{{ number_format($row->cgst,2) }}</td>
            <td class="text-right">{{ number_format($row->sgst,2) }}</td>
            <td class="text-right">{{ number_format($row->igst,2) }}</td>
            <td class="text-right">{{ number_format($row->total_price,2) }}</td>
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
            {{ \Illuminate\Support\Str::ucfirst(\App\Helpers\NumberToWords::convert($invoice->grand_total)) }} Only
        </td>
        <td width="30%">
            <table>
                <tr>
                    <td>Taxable Amount</td>
                    <td class="text-right">{{ number_format($invoice->taxable_amount,2) }}</td>
                </tr>
                <tr>
                    <td>CGST</td>
                    <td class="text-right">{{ number_format($invoice->cgst,2) }}</td>
                </tr>
                <tr>
                    <td>SGST</td>
                    <td class="text-right">{{ number_format($invoice->sgst,2) }}</td>
                </tr>
                <tr>
                    <td>IGST</td>
                    <td class="text-right">{{ number_format($invoice->igst,2) }}</td>
                </tr>
                <tr>
                    <td>Invoice Discount</td>
                    <td class="text-right">{{ number_format($invoice->discount,2) }}</td>
                </tr>
                <tr>
                    <td class="bold">Grand Total</td>
                    <td class="bold text-right">{{ number_format($invoice->grand_total,2) }}</td>
                </tr>
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
        @php
            $grouped = $invoice->items->groupBy('gst_rate');
        @endphp
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
    <p class="small">
        {{ setting('invoice_footer') }}
    </p>

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
