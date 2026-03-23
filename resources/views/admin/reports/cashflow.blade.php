@extends('layouts.admin')

@section('content')

<h4>Cash Flow Report</h4>

<table class="table table-bordered">
    <tr>
        <th>Total Inflow (Payments)</th>
        <td class="text-success">
            {{ number_format($inflow,2) }}
        </td>
    </tr>
    <tr>
        <th>Total Outflow (Expenses)</th>
        <td class="text-danger">
            {{ number_format($outflow,2) }}
        </td>
    </tr>
    <tr>
        <th>Net Cash Flow</th>
        <td>
            <strong class="{{ ($inflow - $outflow) >= 0 ? 'text-success' : 'text-danger' }}">
                {{ number_format($inflow - $outflow,2) }}
            </strong>
        </td>
    </tr>
</table>

@endsection
