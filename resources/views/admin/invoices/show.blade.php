@extends('layouts.admin')

@section('content')


<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Invoice #{{ $invoice->invoice_no }}</h4>
        

        <div class="action_area d-flex gap-2 flex-wrap">

            {{-- =======================
            DRAFT INVOICE ACTIONS
            ======================= --}}
            @if(!$invoice->is_final)

                <a href="{{ route('admin.invoices.edit', $invoice) }}"
                class="btn btn-warning">
                    ✏️ Edit Draft
                </a>

                <form method="POST"
                    action="{{ route('admin.invoices.finalize', $invoice) }}"
                    class="d-inline">
                    @csrf
                    <button class="btn text-dark"
                            onclick="return confirm('Finalize invoice? This cannot be edited later.')">
                        ✅ Finalize Invoice
                    </button>
                </form>

            @else
            {{-- =======================
            FINAL INVOICE ACTIONS
            ======================= --}}

                <a href="{{ route('admin.invoices.pdf', $invoice) }}"
                class="btn btn-outline-success">
                    📄 Download PDF
                </a>

                <a href="{{ route('admin.invoices.ledger', $invoice) }}"
                class="btn btn-outline-primary">
                    📒 Invoice Ledger
                </a>

                <a href="{{ route('admin.clients.ledger', $invoice->client_id) }}"
                class="btn btn-outline-info">
                    👤 Client Ledger
                </a>

                @if(
                    $invoice->status !== 'cancelled'
                )

                <a href="{{ route('admin.credit-notes.create', $invoice) }}"
                class="btn btn-outline-danger">
                    ↩️ Issue Credit Note
                </a>
                @endif

                {{-- =======================
                CANCEL (ONLY IF SAFE)
                ======================= --}}
                @if(
                    $invoice->status !== 'cancelled'
                    && $invoice->payments()->count() === 0
                    && $invoice->creditNotes()->where('status','active')->count() === 0
                )
                    <form action="{{ route('admin.invoices.cancel', $invoice) }}"
                        method="POST"
                        class="d-inline"
                        onsubmit="return confirm('Cancel invoice and restore stock?')">
                        @csrf
                        <button class="btn bg-danger text-light border-danger">
                            ❌ Cancel Invoice
                        </button>
                    </form>
                @endif

            @endif

        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.invoices.index') }}">Invoices</a></li>
            <li class="breadcrumb-item active" aria-current="page">Invoice #{{ $invoice->invoice_no }}</li>
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


{{-- INVOICE SUMMARY --}}
<div class="postbox mb-3">
    <div class="postbox_body">
        <div class="row">
            <div class="col-md-4">
                <h6>Bill To</h6>
                <p class="mb-1"><strong>{{ $invoice->client->company_name }}</strong></p>
                <p class="mb-1"><strong>{{ $invoice->client->user->name }}</strong></p>
                <p class="mb-1">{{ $invoice->client->address }}</p>
                <p class="mb-1">{{ $invoice->client->phone }}</p>
                <p class="mb-0">{{ $invoice->client->user->email }}</p>
            </div>

            <div class="col-md-4">
                <h6>Invoice Info</h6>
                <p class="mb-1">Date: {{ $invoice->invoice_date }}</p>
                {{--<p class="mb-1">Tax Type:
                    <strong>{{ $invoice->is_tax_inclusive ? 'Inclusive' : 'Exclusive' }}</strong>
                </p>--}}
                <p class="mb-1">Status:
                    <span class="badge bg-{{
                        $invoice->status == 'cancelled' ? 'dark' :
                        ($invoice->payment_status == 'paid' ? 'success' :
                        ($invoice->payment_status == 'partial' ? 'warning' : 'danger'))
                    }}">
                        {{ ucfirst($invoice->status == 'cancelled' ? 'Cancelled' : $invoice->payment_status) }}
                    </span>
                </p>
            </div>

            <div class="col-md-4 text-end">
                <h6>Grand Total</h6>
                <h4>₹ {{ number_format($invoice->grand_total, 2) }}</h4>
            </div>
        </div>
    </div>
</div>

{{-- INVOICE ITEMS --}}
<table class="data_table mb-3">
    <thead class="table-light">
        <tr>
            <th>Item</th>
            <th class="text-end">HSN</th>
            <th class="text-center">Qty</th>
            <th class="text-end">Rate</th>
            <th class="text-end">Item Discount</th>
            <th class="text-end">Taxable Value</th>
            <th class="text-end">GST %</th>
            <th class="text-end">CGST</th>
            <th class="text-end">SGST</th>
            <th class="text-end">IGST</th>
            <th class="text-end">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->items as $item)
            <tr>
                <td>{{ $item->item->name }}</td>
                <td class="text-end">{{ $item->hsn }}</td>
                <td class="text-center">{{ $item->quantity }}</td>
                <td class="text-end">₹ {{ number_format($item->unit_price, 2) }}</td>
                <td class="text-end">
                    @if($item->discount_type === 'percent')
                        {{ $item->discount_value }}%
                    @else
                        ₹ {{ number_format($item->discount_value,2) }}
                    @endif
                </td>
                <td class="text-end">{{ number_format($item->taxable_amount,2) }}</td>
                <td class="text-end">{{ $item->gst_rate }}%</td>
                <td class="text-end">{{ number_format($item->cgst,2) }}</td>
                <td class="text-end">{{ number_format($item->sgst,2) }}</td>
                <td class="text-end">{{ number_format($item->igst,2) }}</td>
                <td class="text-end">{{ number_format($item->total_price,2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="10" class="text-end">Subtotal (After Item Discount)</th>
            <th class="text-end">₹ {{ number_format($invoice->subtotal, 2) }}</th>
        </tr>
        <tr>
            <th colspan="10" class="text-end">CGST</td>
            <th class="text-end">{{ number_format($invoice->cgst,2) }}</td>
        </tr>
        <tr>
            <th colspan="10" class="text-end">SGST</td>
            <th class="text-end">{{ number_format($invoice->sgst,2) }}</td>
        </tr>
        <tr>
            <th colspan="10" class="text-end">IGST</td>
            <th class="text-end">{{ number_format($invoice->igst,2) }}</td>
        </tr>
        <tr>
            <th colspan="10" class="text-end text-success">Invoice Discount</th>
            <th class="text-end text-success">- ₹ {{ number_format($invoice->discount, 2) }}</th>
        </tr>
        <tr class="table-success">
            <th colspan="10" class="text-end">Grand Total</th>
            <th class="text-end">₹ {{ number_format($invoice->grand_total, 2) }}</th>
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
        @if($invoice->payments->count())
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
                    @foreach($invoice->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_date }}</td>
                            <td>{{ $payment->payment_method }}</td>
                            <td class="text-end">₹ {{ number_format($payment->amount, 2) }}</td>
                            <td class="text-end">
                                    <a href="{{ route('admin.payments.receipt', $payment->id) }}"
                                    class="btn btn-sm btn-secondary">
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
@if($invoice->is_final)
@if($invoice->payment_status !== 'paid')
@if($invoice->status === 'cancelled')
@else
    <div class="postbox mb-3">
        <div class="postbox_header">
            <h3>Add Payment</h3>
            <a href="javascript:void(0)" class="postbox_toggle">
                <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
            </a>
        </div>
        <form action="{{ route('admin.invoices.payment', $invoice->id) }}" method="POST">
            @csrf
            <div class="postbox_body">
                <div class="row">
                    <div class="col-md-auto">
                        <div class="form_group">
                            <label>Amount</label>
                            <input type="number" name="amount" min="0" step="0.01" class="textbox" max="{{ $invoice->grand_total - $invoice->payments->sum('amount') }}" required>
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
@endif
@endif

{{-- CREDIT NOTES --}}
<div class="postbox">
    <div class="postbox_header">
        <h3>Credit Notes</h3>
        <a href="javascript:void(0)" class="postbox_toggle">
            <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
        </a>
    </div>
    <div class="postbox_body">
        @if($invoice->creditNotes->count())
            <table class="data_table">
                <thead>
                    <tr>
                        <th>Credit Note No</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->creditNotes as $cn)
                        <tr>
                            <td>{{ $cn->credit_note_no }}</td>
                            <td>{{ $cn->credit_date->format('Y-m-d') }}</td>
                            <td>₹ {{ number_format($cn->grand_total, 2) }}</td>
                            <td>{{ $cn->reason }}</td>
                            <td>
                                @if($cn->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($cn->status === 'cancelled')
                                    <span class="badge bg-danger">Cancelled</span>
                                @elseif($cn->status === 'reversal')
                                    <span class="badge bg-info">Reversal</span>
                                @endif
                            </td>
                            <td>
                                {{-- Cancel button --}}
                                @if($cn->status === 'active' && !$cn->locked)
                                    <form action="{{ route('admin.credit-notes.cancel', $cn->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Cancel this credit note?')">
                                        @csrf
                                        @method('POST')
                                        <button class="btn btn-sm btn-danger">Cancel</button>
                                    </form>
                                @endif

                                {{-- Reversal button --}}
                                @if($cn->status === 'cancelled' && !$cn->reversal_created)
                                    <form action="{{ route('admin.credit-notes.reversal', $cn->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Create reversal for this credit note?')">
                                        @csrf
                                        <input type="text" name="reason" placeholder="Reason" required class="form-control form-control-sm d-inline w-auto">
                                        <button class="btn btn-sm btn-warning">Reversal</button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.credit-notes.pdf', $cn->id) }}" class="btn btn-success" target="_blank">
                                    Download PDF
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted">No credit notes for this invoice.</p>
        @endif
    </div>
</div>

</div>
@endsection
