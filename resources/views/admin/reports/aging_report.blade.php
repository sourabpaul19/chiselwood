@extends('layouts.admin')

@section('content')

<h3>Aging Report</h3>

<table class="table table-bordered">
<thead>
<tr>
    <th>Invoice</th>
    <th>Client</th>
    <th>Date</th>
    <th>Amount</th>
    <th>Days</th>
    <th>Bucket</th>
</tr>
</thead>
<tbody>
@foreach($data as $row)
<tr>
    <td>{{ $row->invoice_no }}</td>
    <td>{{ $row->client_id }}</td>
    <td>{{ $row->invoice_date }}</td>
    <td>{{ number_format($row->grand_total,2) }}</td>
    <td>{{ $row->days_due }}</td>
    <td>
        <span class="badge bg-warning">{{ $row->bucket }}</span>
    </td>
</tr>
@endforeach
</tbody>
</table>

@endsection
