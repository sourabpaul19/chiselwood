<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Credit Note {{ $creditNote->credit_note_no }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
        }
        th {
            background: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .no-border {
            border: none;
        }
        .header-table td {
            border: none;
            vertical-align: top;
        }
        .small {
            font-size: 11px;
        }
    </style>
</head>
<body>

{{-- ================= HEADER ================= --}}
<table class="header-table">
    <tr>
        <td width="60%">
            <h2>Credit Note</h2>
            <p class="small">
                <strong>Credit Note No:</strong> {{ $creditNote->credit_note_no }}<br>
                <strong>Date:</strong> {{ $creditNote->created_at->format('d-m-Y') }}<br>
                <strong>Invoice Ref:</strong> {{ $creditNote->invoice->invoice_no }}
            </p>
        </td>
        <td width="40%">
            <p class="small">
                <strong>Client:</strong> <br/><strong>{{ $creditNote->client->company_name }}</strong><br>
            {{ $creditNote->client->user->name }}<br>
            {{ $creditNote->client->address ?? '' }}<br>
            GSTIN: {{ $creditNote->client->gstin ?? 'Unregistered' }}<br>
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
    ][$creditNote->client->client_state ?? ''] ?? 'Unknown State'
}}
                
            </p>
        </td>
    </tr>
</table>

{{-- ================= CLIENT ================= --}}
<table>
    <tr>
        <td width="50%">
            <strong>Credit To:</strong><br>
            {{ $creditNote->client->company_name }}<br>
            {{ $creditNote->client->user->name }}<br>
            {{ $creditNote->client->address }}<br>
            GSTIN: {{ $creditNote->client->gstin ?? 'Unregistered' }}<br>
            State: {{ $creditNote->client->client_state }}
        </td>
        <td width="50%">
            <strong>Reason:</strong><br>
            {{ $creditNote->reason }}
        </td>
    </tr>
</table>

{{-- ================= ITEMS ================= --}}
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Description</th>
            <th>Qty</th>
            <th>Unit Price<br>(Incl. GST)</th>
            <th>Taxable Amount</th>
            <th>Total<br>(Incl. GST)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($creditNote->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->inventoryItem->name }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">{{ number_format($item->taxable_amount, 2) }}</td>
                <td class="text-right">{{ number_format($item->total_price, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- ================= TOTALS ================= --}}
@php
    $sameState = strtolower($creditNote->client->client_state)
        === strtolower(config('app.company_state', 'WB'));
@endphp

<table>
    <tr>
        <td class="no-border" width="60%"></td>
        <td class="text-right">Taxable Subtotal</td>
        <td class="text-right">{{ number_format($creditNote->subtotal, 2) }}</td>
    </tr>

    @if($creditNote->discount > 0)
    <tr>
        <td class="no-border"></td>
        <td class="text-right">Discount (Incl. GST)</td>
        <td class="text-right">{{ number_format($creditNote->discount, 2) }}</td>
    </tr>
    @endif

    @if($sameState)
        <tr>
            <td class="no-border"></td>
            <td class="text-right">CGST</td>
            <td class="text-right">{{ number_format($creditNote->cgst, 2) }}</td>
        </tr>
        <tr>
            <td class="no-border"></td>
            <td class="text-right">SGST</td>
            <td class="text-right">{{ number_format($creditNote->sgst, 2) }}</td>
        </tr>
    @else
        <tr>
            <td class="no-border"></td>
            <td class="text-right">IGST</td>
            <td class="text-right">{{ number_format($creditNote->igst, 2) }}</td>
        </tr>
    @endif

    <tr>
        <td class="no-border"></td>
        <td class="text-right"><strong>Grand Total (Incl. GST)</strong></td>
        <td class="text-right">
            <strong>{{ number_format($creditNote->grand_total, 2) }}</strong>
        </td>
    </tr>
</table>

{{-- ================= FOOTER ================= --}}
<p class="small" style="margin-top: 20px;">
    <strong>Note:</strong><br>
    All prices are inclusive of GST. This credit note has been issued
    against Invoice {{ $creditNote->invoice->invoice_no }} and proportionately
    reverses the taxable value and GST. No cash refund is involved unless
    explicitly mentioned.
</p>

<p class="small">
    Generated on {{ now()->format('d-m-Y H:i') }}
</p>

</body>
</html>
