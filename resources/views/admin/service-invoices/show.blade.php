@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Service Invoice #{{ $serviceInvoice->invoice_no }}</h4>

        <div class="action_area d-flex gap-2 flex-wrap">
            @if(!$serviceInvoice->is_final)
                <a href="{{ route('admin.service-invoices.edit', $serviceInvoice) }}" class="btn btn-warning">
                    ✏️ Edit Draft
                </a>

                <form method="POST" action="{{ route('admin.service-invoices.finalize', $serviceInvoice) }}" class="d-inline">
                    @csrf
                    <button class="btn text-dark" onclick="return confirm('Finalize invoice? This cannot be edited later.')">
                        ✅ Finalize Invoice
                    </button>
                </form>
            @else
                <a href="{{ route('admin.service-invoices.pdf', $serviceInvoice) }}" class="btn btn-outline-success">
                    📄 Download PDF
                </a>

                <a href="{{ route('admin.service-invoices.ledger', $serviceInvoice) }}" class="btn btn-outline-primary">
                    📒 Invoice Ledger
                </a>

                <a href="{{ route('admin.clients.ledger', $serviceInvoice->client_id) }}" class="btn btn-outline-info">
                    👤 Client Ledger
                </a>

                @if($serviceInvoice->status !== 'cancelled'
                    && $serviceInvoice->payments()->count() === 0)
                    <form action="{{ route('admin.service-invoices.cancel', $serviceInvoice) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel invoice?')">
                        @csrf
                        <button class="btn bg-danger text-light border-danger">❌ Cancel Invoice</button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.service-invoices.index') }}">Service Invoices</a></li>
            <li class="breadcrumb-item active" aria-current="page">Invoice #{{ $serviceInvoice->invoice_no }}</li>
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

{{-- SERVICE INVOICE SUMMARY --}}
<div class="postbox mb-3">
    <div class="postbox_body">
        <div class="row">
            <div class="col-md-4">
                <h6>Bill To</h6>
                <p class="mb-1"><strong>{{ $serviceInvoice->client->company_name }}</strong></p>
                <p class="mb-1"><strong>{{ $serviceInvoice->client->user->name }}</strong></p>
                <p class="mb-1">{{ $serviceInvoice->client->address }}</p>
                <p class="mb-1">{{ $serviceInvoice->client->phone }}</p>
                <p class="mb-0">{{ $serviceInvoice->client->user->email }}</p>
            </div>

            <div class="col-md-4">
                <h6>Invoice Info</h6>
                <p class="mb-1">Date: {{ $serviceInvoice->invoice_date }}</p>
                {{--<p class="mb-1">Tax Type: <strong>{{ $serviceInvoice->is_tax_inclusive ? 'Inclusive' : 'Exclusive' }}</strong></p>--}}
                <p class="mb-1">Status:
                    <span class="badge bg-{{
                        $serviceInvoice->status == 'cancelled' ? 'dark' :
                        ($serviceInvoice->payment_status == 'paid' ? 'success' :
                        ($serviceInvoice->payment_status == 'partial' ? 'warning' : 'danger'))
                    }}">
                        {{ ucfirst($serviceInvoice->status == 'cancelled' ? 'Cancelled' : $serviceInvoice->payment_status) }}
                    </span>
                </p>
            </div>

            <div class="col-md-4 text-end">
                <h6>Grand Total</h6>
                <h4>₹ {{ number_format($serviceInvoice->grand_total, 2) }}</h4>
            </div>
        </div>
    </div>
</div>

{{-- SERVICE ITEMS --}}
<table class="data_table mb-3">
    <thead class="table-light">
        <tr>
            <th>Service</th>
            <th>Description</th>
            <th class="text-center">Hours / Units</th>
            <th class="text-end">Rate</th>
            <th class="text-end">Discount</th>
            <th class="text-end">Taxable Value</th>
            <th class="text-end">GST %</th>
            <th class="text-end">CGST</th>
            <th class="text-end">SGST</th>
            <th class="text-end">IGST</th>
            <th class="text-end">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($serviceInvoice->items as $service)
        <tr>
            <td>{{ $service->service_name }}</td>
            <td>{{ $service->description }}</td>
            <td class="text-center">{{ $service->units }}</td>
            <td class="text-end">₹ {{ number_format($service->rate,2) }}</td>
            <td class="text-end">
                @if($service->discount_type === 'percent')
                    {{ $service->discount_value }}%
                @else
                    ₹ {{ number_format($service->discount_value,2) }}
                @endif
            </td>
            <td class="text-end">{{ number_format($service->taxable_amount,2) }}</td>
            <td class="text-end">{{ $service->gst_rate }}%</td>
            <td class="text-end">{{ number_format($service->cgst,2) }}</td>
            <td class="text-end">{{ number_format($service->sgst,2) }}</td>
            <td class="text-end">{{ number_format($service->igst,2) }}</td>
            <td class="text-end">{{ number_format($service->total_price,2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="10" class="text-end">Subtotal (After Service Discount)</th>
            <th class="text-end">₹ {{ number_format($serviceInvoice->subtotal,2) }}</th>
        </tr>
        <tr>
            <th colspan="10" class="text-end">CGST</th>
            <th class="text-end">{{ number_format($serviceInvoice->cgst,2) }}</th>
        </tr>
        <tr>
            <th colspan="10" class="text-end">SGST</th>
            <th class="text-end">{{ number_format($serviceInvoice->sgst,2) }}</th>
        </tr>
        <tr>
            <th colspan="10" class="text-end">IGST</th>
            <th class="text-end">{{ number_format($serviceInvoice->igst,2) }}</th>
        </tr>
        <tr>
            <th colspan="10" class="text-end text-success">Invoice Discount</th>
            <th class="text-end text-success">- ₹ {{ number_format($serviceInvoice->discount,2) }}</th>
        </tr>
        <tr class="table-success">
            <th colspan="10" class="text-end">Grand Total</th>
            <th class="text-end">₹ {{ number_format($serviceInvoice->grand_total,2) }}</th>
        </tr>
    </tfoot>
</table>

{{-- PAYMENTS --}}
<div class="postbox mb-3">
    <div class="postbox_header">
        <h3>Payments</h3>
        <a href="javascript:void(0)" class="postbox_toggle">
            <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
        </a>
    </div>
    <div class="postbox_body">
        @if($serviceInvoice->payments->count())
        <table class="data_table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Method</th>
                    <th class="text-end">Amount</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($serviceInvoice->payments as $payment)
                <tr>
                    <td>{{ $payment->payment_date }}</td>
                    <td>{{ $payment->payment_method }}</td>
                    <td class="text-end">₹ {{ number_format($payment->amount,2) }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.payments.receipt', $payment->id) }}" class="btn btn-sm btn-secondary">
                            Download Receipt
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="text-muted">No payments recorded yet.</p>
        @endif
    </div>
</div>

{{-- ADD PAYMENT --}}
@if($serviceInvoice->is_final && $serviceInvoice->payment_status !== 'paid' && $serviceInvoice->status !== 'cancelled')
<div class="postbox mb-3">
    <div class="postbox_header">
        <h3>Add Payment</h3>
        <a href="javascript:void(0)" class="postbox_toggle">
            <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
        </a>
    </div>
    <form action="{{ route('admin.service-invoices.payment', $serviceInvoice->id) }}" method="POST">
        @csrf
        <div class="postbox_body">
            <div class="row">
                <div class="col-md-auto">
                    <div class="form_group">
                        <label>Amount</label>
                        <input type="number" name="amount" min="0" step="0.01" class="textbox" max="{{ $serviceInvoice->grand_total - $serviceInvoice->payments->sum('amount') }}" required>
                    </div>
                </div>
                <div class="col-md-auto">
                    <div class="form_group">
                        <label>Payment Method</label>
                        <select name="payment_method" class="textbox">
                            <option value="Cash">Cash</option>
                            <option value="UPI">UPI</option>
                            <option value="Card">Card</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-auto align-self-end">
                    <button class="btn bth-theme">Save Payment</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endif


@endsection