@extends('layouts.client')

@section('content')
<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Invoice #{{ $invoice->id }} Receipts</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Invoices</li>
        </ol>
    </nav>
</div>

<table class="data_table">
    <thead>
        <tr>
            <th>Receipt No</th>
            <th>Amount Paid</th>
            <th>Payment Method</th>
            <th>Payment Date</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($receipts as $receipt)
        <tr>
            <td>{{ $receipt->receipt_no }}</td>
            <td>{{ $receipt->amount }}</td>
            <td>{{ $receipt->payment_method }}</td>
            <td>{{ $receipt->payment_date }}</td>
            <td>
                <a href="{{ route('client.receipt.download', $receipt->id) }}" 
                class="btn" target="_blank">
                Download PDF
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection