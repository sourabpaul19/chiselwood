@extends('layouts.admin')

@section('content')

<h3>Balance Sheet</h3>

<div class="row">

    {{-- ASSETS --}}
    <div class="col-md-6">
        <h4>Assets</h4>
        <table class="table table-bordered">
            <tr>
                <th>Cash</th>
                <td>{{ number_format($cash,2) }}</td>
            </tr>
            <tr>
                <th>Inventory</th>
                <td>{{ number_format($inventoryValue,2) }}</td>
            </tr>
            <tr>
                <th>Accounts Receivable</th>
                <td>{{ number_format($receivables,2) }}</td>
            </tr>
            <tr class="table-success">
                <th>Total Assets</th>
                <th>{{ number_format($totalAssets,2) }}</th>
            </tr>
        </table>
    </div>

    {{-- LIABILITIES + EQUITY --}}
    <div class="col-md-6">
        <h4>Liabilities & Equity</h4>
        <table class="table table-bordered">
            <tr>
                <th>Vendor Payables</th>
                <td>{{ number_format($payables,2) }}</td>
            </tr>
            <tr>
                <th>Owner Equity (Net Profit)</th>
                <td>{{ number_format($equity,2) }}</td>
            </tr>
            <tr class="table-success">
                <th>Total Liabilities + Equity</th>
                <th>{{ number_format($payables + $equity,2) }}</th>
            </tr>
        </table>
    </div>

</div>

@endsection
