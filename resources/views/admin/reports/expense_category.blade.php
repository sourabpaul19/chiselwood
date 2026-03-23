@extends('layouts.admin')

@section('content')

<h4>Category Wise Expense Report</h4>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Category</th>
            <th>Total Expense</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
        <tr>
            <td>{{ $row->name }}</td>
            <td>{{ number_format($row->total,2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
