@extends('layouts.admin')

@section('content')

<h3>Inventory Valuation (FIFO)</h3>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Product</th>
            <th>Stock Qty</th>
            <th>Stock Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
        <tr>
            <td>{{ $row->name }}</td>
            <td>{{ $row->total_qty }}</td>
            <td>{{ number_format($row->stock_value,2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h4>Total Stock Value: {{ number_format($totalValue,2) }}</h4>

@endsection
