@extends('layouts.admin')

@section('content')

<h4>Outstanding Receivables</h4>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Invoice No</th>
            <th>Client</th>
            <th>Grand Total</th>
            <th>Paid</th>
            <th>Due</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
        <tr>
            <td>{{ $row['invoice_no'] }}</td>
            <td>{{ $row['client_id'] }}</td>
            <td>{{ number_format($row['grand_total'],2) }}</td>
            <td>{{ number_format($row['paid'],2) }}</td>
            <td class="text-danger">
                {{ number_format($row['due'],2) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
