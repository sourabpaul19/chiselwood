@extends('layouts.admin')

@section('content')

<h3>GST Wise Revenue</h3>

<form method="GET" class="row mb-4">
    <div class="col-md-3">
        <label>From</label>
        <input type="date" name="from"
               value="{{ $from ?? '' }}"
               class="form-control">
    </div>

    <div class="col-md-3">
        <label>To</label>
        <input type="date" name="to"
               value="{{ $to ?? '' }}"
               class="form-control">
    </div>

    <div class="col-md-2 align-self-end">
        <button class="btn btn-primary">Filter</button>
    </div>
</form>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>GST %</th>
            <th>Taxable Amount</th>
            <th>CGST</th>
            <th>SGST</th>
            <th>IGST</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
        <tr>
            <td>{{ $row->gst_rate }}%</td>
            <td>{{ number_format($row->taxable,2) }}</td>
            <td>{{ number_format($row->cgst,2) }}</td>
            <td>{{ number_format($row->sgst,2) }}</td>
            <td>{{ number_format($row->igst,2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
