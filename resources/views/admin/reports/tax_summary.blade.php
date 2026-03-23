@extends('layouts.admin')

@section('content')

<h4>GST Tax Summary</h4>

<table class="table table-bordered">
    <tr>
        <th>Total CGST</th>
        <td>{{ number_format($tax->total_cgst ?? 0,2) }}</td>
    </tr>
    <tr>
        <th>Total SGST</th>
        <td>{{ number_format($tax->total_sgst ?? 0,2) }}</td>
    </tr>
    <tr>
        <th>Total IGST</th>
        <td>{{ number_format($tax->total_igst ?? 0,2) }}</td>
    </tr>
</table>

@endsection
