@extends('layouts.admin')

@section('content')

<h3>Profit & Loss Statement</h3>
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
<a href="{{ route('admin.reports.profit.loss.export', request()->query()) }}"
   class="btn btn-success mb-3">
   Export Excel
</a>

<table class="table table-bordered">
    <tr><th>Total Revenue</th><td>{{ number_format($revenue,2) }}</td></tr>
    <tr><th>Sales Returns</th><td class="text-danger">- {{ number_format($returns,2) }}</td></tr>
    <tr><th>Net Revenue</th><td>{{ number_format($netRevenue,2) }}</td></tr>
    <tr><th>COGS</th><td class="text-danger">- {{ number_format($cogs,2) }}</td></tr>
    <tr><th>Gross Profit</th><td>{{ number_format($grossProfit,2) }}</td></tr>
    <tr><th>Expenses</th><td class="text-danger">- {{ number_format($expenses,2) }}</td></tr>
    <tr class="table-success">
        <th>Net Profit</th>
        <th>{{ number_format($netProfit,2) }}</th>
    </tr>
</table>

@endsection
