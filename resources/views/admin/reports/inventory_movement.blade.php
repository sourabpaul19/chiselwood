@extends('layouts.admin')

@section('content')

<h4>Inventory Movement - {{ $item->name }}</h4>

<table class="table table-bordered">
<thead>
<tr>
    <th>Date</th>
    <th>Type</th>
    <th>Reference</th>
    <th>Qty</th>
    <th>Running Balance</th>
</tr>
</thead>
<tbody>
@foreach($transactions as $t)
<tr>
    <td>{{ $t->created_at }}</td>
    <td>{{ $t->reference_type }}</td>
    <td>{{ $t->note }}</td>
    <td class="{{ $t->type == 'OUT' ? 'text-danger' : 'text-success' }}">
        {{ $t->type == 'OUT' ? '-' : '+' }}{{ $t->quantity }}
    </td>
    <td>{{ $t->running_balance }}</td>
</tr>
@endforeach
</tbody>
</table>


@endsection
