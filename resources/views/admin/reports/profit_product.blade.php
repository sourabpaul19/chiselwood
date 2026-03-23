@extends('layouts.admin')

@section('content')

<h3>Profit by Product</h3>
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

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Product</th>
            <th>Revenue</th>
            <th>Cost</th>
            <th>Profit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $row)
        <tr>
            <td>{{ $row->name }}</td>
            <td>{{ number_format($row->revenue,2) }}</td>
            <td>{{ number_format($row->cost,2) }}</td>
            <td class="{{ $row->profit >= 0 ? 'text-success':'text-danger' }}">
                {{ number_format($row->profit,2) }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
