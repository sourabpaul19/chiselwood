@extends('layouts.client')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>All Invoices</h4>
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
                <th>#</th>
                <th>Invoice No</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Payment Status</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $key => $invoice)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $invoice->invoice_no }}</td>
                    <td>₹{{ $invoice->grand_total }}</td>
                    <td>{{ $invoice->status }}</td>
                    <td>{{ $invoice->payment_status }}</td>
                    <td>{{ $invoice->created_at->format('d M Y') }}</td>
                    <td>
                        @if($invoice->payments->count() > 0)
                            <a href="{{ route('client.invoice.receipts', $invoice->id) }}" class="btn">
                                Receipt
                            </a>
                        @endif
                        <a href="{{ route('client.invoice.pdf', $invoice->id) }}" class="btn">
                            Download PDF
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No invoices found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection