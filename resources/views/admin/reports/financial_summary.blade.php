@extends('layouts.admin')

@section('content')

<h3>Financial Summary Report</h3>

<form method="GET" class="row mb-4">
    <div class="col-md-3">
        <label>From</label>
        <input type="date" name="from" value="{{ $from }}" class="form-control">
    </div>
    <div class="col-md-3">
        <label>To</label>
        <input type="date" name="to" value="{{ $to }}" class="form-control">
    </div>
    <div class="col-md-2 align-self-end">
        <button class="btn btn-primary">Filter</button>
    </div>
</form>

<table class="table table-bordered">
    <tr>
        <th>Total Revenue</th>
        <td>{{ number_format($totalRevenue, 2) }}</td>
    </tr>
    <tr>
        <th>Sales Returns</th>
        <td class="text-danger">
            - {{ number_format($totalReturns, 2) }}
        </td>
    </tr>
    <tr>
        <th>Net Revenue</th>
        <td>{{ number_format($netRevenue, 2) }}</td>
    </tr>
    <tr>
        <th>Cost of Goods Sold (COGS)</th>
        <td class="text-danger">
            - {{ number_format($totalCogs, 2) }}
        </td>
    </tr>
    <tr>
        <th>Gross Profit</th>
        <td>
            {{ number_format($grossProfit, 2) }}
        </td>
    </tr>
    <tr>
        <th>Operating Expenses</th>
        <td class="text-danger">
            - {{ number_format($totalExpenses, 2) }}
        </td>
    </tr>
    <tr>
        <th><strong>Net Profit</strong></th>
        <td>
            <strong class="{{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                {{ number_format($netProfit, 2) }}
            </strong>
        </td>
    </tr>
</table>

@endsection
