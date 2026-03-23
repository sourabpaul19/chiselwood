@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>GSTR-1 Report</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">GSTR-1 Report</li>
        </ol>
    </nav>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<form method="GET" class="row g-2 mb-3">
    <div class="col-md-auto">
        <input type="date" name="from" value="{{ $from }}" class="textbox">
    </div>
    <div class="col-md-auto">
        <input type="date" name="to" value="{{ $to }}" class="textbox">
    </div>
    <div class="col-md-auto">
        <button class="btn btn-theme">Generate</button>
        <a href="{{ url('/gstr1/export?from='.$from.'&to='.$to) }}"
           class="btn text-success">
            Download Excel
        </a>
    </div>
</form>

<hr>

<div class="postbox mb-3">
    <div class="postbox_header">
        <h3>B2B Summary</h3>
        <a href="javascript:void(0)" class="postbox_toggle">
            <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
        </a>
    </div>
    <div class="postbox_body">
        <table class="data_table">
            <thead>
                <tr>
                    <th>Invoices</th>
                    <th>Taxable</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th>IGST</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $b2b->count() }}</td>
                    <td>{{ number_format($b2b->sum(fn($i)=>$i->items->sum('taxable_amount')),2) }}</td>
                    <td>{{ number_format($b2b->sum(fn($i)=>$i->items->sum('cgst')),2) }}</td>
                    <td>{{ number_format($b2b->sum(fn($i)=>$i->items->sum('sgst')),2) }}</td>
                    <td>{{ number_format($b2b->sum(fn($i)=>$i->items->sum('igst')),2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<div class="postbox mb-3">
    <div class="postbox_header">
        <h3>B2C Summary</h3>
        <a href="javascript:void(0)" class="postbox_toggle">
            <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
        </a>
    </div>
    <div class="postbox_body">
        <table class="data_table">
            <tr>
                <td>{{ $b2c->count() }}</td>
                <td>{{ number_format($b2c->sum(fn($i)=>$i->items->sum('taxable_amount')),2) }}</td>
            </tr>
        </table>
    </div>
</div>


<div class="postbox mb-3">
    <div class="postbox_header">
        <h3>Credit Notes</h3>
        <a href="javascript:void(0)" class="postbox_toggle">
            <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
        </a>
    </div>
    <div class="postbox_body">
        <table class="data_table">
            <thead>
                <tr>
                    <th>Count</th>
                    <th>Taxable</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $creditNotes->count() }}</td>
                    <td>{{ number_format($creditNotes->sum(fn($c)=>$c->items->sum('taxable_amount')),2) }}</td>
                </tr>
            <tbody>
        </table>
    </div>
</div>

<div class="postbox mb-3">
    <div class="postbox_header">
        <h3>GST Summary</h3>
        <a href="javascript:void(0)" class="postbox_toggle">
            <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
        </a>
    </div>
    <div class="postbox_body">
        <table class="data_table">
            <thead>
                <tr>
                    <th>Taxable</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th>IGST</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ number_format($summary['taxable'],2) }}</td>
                    <td>{{ number_format($summary['cgst'],2) }}</td>
                    <td>{{ number_format($summary['sgst'],2) }}</td>
                    <td>{{ number_format($summary['igst'],2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>


<div class="postbox mb-3">
    <div class="postbox_header">
        <h3>HSN Summary</h3>
        <a href="javascript:void(0)" class="postbox_toggle">
            <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
        </a>
    </div>
    <div class="postbox_body">
        <table class="data_table">
            <thead>
                <tr>
                    <th>HSN</th>
                    <th>Description</th>
                    <th>UQC</th>
                    <th>Qty</th>
                    <th>Taxable Value</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th>IGST</th>
                </tr>
            </thead>
            <tbody>
                @foreach($hsnSummary as $row)
                    <tr>
                        <td>{{ $row['hsn'] }}</td>
                        <td>{{ $row['desc'] }}</td>
                        <td>{{ $row['uqc'] }}</td>
                        <td>{{ number_format($row['qty'], 2) }}</td>
                        <td>{{ number_format($row['taxable'], 2) }}</td>
                        <td>{{ number_format($row['cgst'], 2) }}</td>
                        <td>{{ number_format($row['sgst'], 2) }}</td>
                        <td>{{ number_format($row['igst'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


<div class="postbox mb-3">
    <div class="postbox_header">
        <h3>GST Rate-wise Summary</h3>
        <a href="javascript:void(0)" class="postbox_toggle">
            <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
        </a>
    </div>
    <div class="postbox_body">
        <table class="data_table">
            <thead>
                <tr>
                    <th>GST %</th>
                    <th>Taxable Value</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th>IGST</th>
                    <th>Total Tax</th>
                </tr>
            </thead>
            <tbody>
                @foreach($gstRateSummary as $row)
                    <tr>
                        <td>{{ $row['rate'] }}%</td>
                        <td>{{ number_format($row['taxable'], 2) }}</td>
                        <td>{{ number_format($row['cgst'], 2) }}</td>
                        <td>{{ number_format($row['sgst'], 2) }}</td>
                        <td>{{ number_format($row['igst'], 2) }}</td>
                        <td>{{ number_format($row['totalTax'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>


@endsection
