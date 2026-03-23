@extends('layouts.admin')

@section('content')

<h4>Stock Aging Report</h4>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Item</th>
            <th>Qty</th>
            <th>Days Old</th>
        </tr>
    </thead>
    <tbody>
        @foreach($report as $r)
        <tr>
            <td>{{ $r['item'] }}</td>
            <td>{{ $r['quantity'] }}</td>
            <td>{{ $r['days'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
