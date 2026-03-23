@extends('layouts.admin')

@section('content')

<h3>Year Comparison</h3>

<table class="table table-bordered">
    <tr>
        <th>This Year Revenue</th>
        <td>{{ number_format($thisYear,2) }}</td>
    </tr>
    <tr>
        <th>Last Year Revenue</th>
        <td>{{ number_format($previousYear,2) }}</td>
    </tr>
    <tr>
        <th>Growth %</th>
        <td class="{{ $growth >= 0 ? 'text-success':'text-danger' }}">
            {{ number_format($growth,2) }} %
        </td>
    </tr>
</table>

@endsection
