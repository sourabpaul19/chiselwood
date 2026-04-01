@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Invoice Management</h4>
        <div class="action_area">
            <a href="{{ route('admin.invoices.create') }}" class="btn ms-auto">Create Invoice</a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Invoice Management</li>
        </ol>
    </nav>
</div>


@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
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
<td>{{ $invoice->client->user->name }}</td>
<td>{{ $invoice->client->company_name }}</td>
<td>{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d-m-Y') }}</td>
<td>{{ $invoice->grand_total }}</td>
<td>
    <span class="text-{{ 
    $invoice->status == 'cancelled' ? 'danger' :
    ($invoice->payment_status == 'paid' ? 'success' :
    ($invoice->payment_status == 'partial' ? 'warning' : 'danger'))
}}">
    {{ ucfirst($invoice->status == 'cancelled' ? 'Cancelled' : $invoice->payment_status) }}
</span>
</td>
<td>{{ ucfirst($invoice->payment_status) }}</td>
<td>
<a href="{{ route('admin.invoices.show',$invoice) }}" class="btn">View</a>
    
    @if(!$invoice->is_final)
    <a href="{{ route('admin.invoices.edit', $invoice->id) }}"
       class="btn">
        Edit
    </a>
    @else
    <a href="{{ route('admin.invoices.pdf', $invoice->id) }}"
   class="btn">
    Download PDF
</a>
@endif

    
</td>
</tr>
@endforeach
</tbody>
</table>

@endsection
