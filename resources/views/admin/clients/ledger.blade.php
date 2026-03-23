@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Client Ledger: {{ $client->company_name }}</h4>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
            <li class="breadcrumb-item active">Client Ledger: {{ $client->company_name }}</li>
        </ol>
    </nav>
</div>


<table class="data_table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Reference</th>
            <th class="text-end">Debit</th>
            <th class="text-end">Credit</th>
            <th class="text-end">Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ledger as $row)
            <tr>
                <td>{{ \Carbon\Carbon::parse($row->date)->format('d-m-Y') }}</td>
                <td>{{ $row->type }}</td>
                <td>{{ $row->reference }}</td>
                <td class="text-end">{{ $row->debit ? number_format($row->debit,2) : '-' }}</td>
                <td class="text-end">{{ $row->credit ? number_format($row->credit,2) : '-' }}</td>
                <td class="text-end">{{ number_format($row->balance,2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@endsection