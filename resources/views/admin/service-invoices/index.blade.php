@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Service Invoice Management</h4>
        <div class="action_area">
            <a href="{{ route('admin.service-invoices.create') }}" class="btn ms-auto">
                Create Service Invoice
            </a>
        </div>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Service Invoice Management
            </li>
        </ol>
    </nav>
</div>

{{-- ERROR --}}
@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

{{-- SUCCESS --}}
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif


<table class="data_table">
    <thead>
        <tr>
            <th>Invoice No</th>
            <th>Client Name</th>
            <th>Company Name</th>
            <th>Invoice Date</th>
            <th>Invoice Amount</th>
            <th>Invoice Status</th>
            <th>Payment Status</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($invoices as $invoice)
        <tr>
            <td>{{ $invoice->invoice_no }}</td>

            <td>
                {{ optional($invoice->client->user)->name }}
            </td>

            <td>{{ $invoice->client->company_name }}</td>

            <td>
                {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') }}
            </td>

            <td>{{ $invoice->grand_total }}</td>

            {{-- STATUS --}}
            <td>
                <span class="text-{{
                    $invoice->status == 'cancelled' ? 'danger' :
                    ($invoice->payment_status == 'paid' ? 'success' :
                    ($invoice->payment_status == 'partial' ? 'warning' : 'danger'))
                }}">
                    {{ ucfirst($invoice->status == 'cancelled' ? 'Cancelled' : $invoice->payment_status) }}
                </span>
            </td>

            {{-- PAYMENT STATUS --}}
            <td>{{ ucfirst($invoice->payment_status) }}</td>

            {{-- ACTION --}}
            <td>

                {{-- VIEW --}}
                <a href="{{ route('admin.service-invoices.show',$invoice->id) }}" class="btn">
                    View
                </a>

                {{-- EDIT (ONLY IF NOT FINAL) --}}
                @if(!$invoice->is_final)
                    <a href="{{ route('admin.service-invoices.edit', $invoice->id) }}" class="btn">
                        Edit
                    </a>
                @else
                    {{-- PDF --}}
                    <a href="{{ route('admin.service-invoices.pdf', $invoice->id) }}" class="btn">
                        Download PDF
                    </a>
                @endif

            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection