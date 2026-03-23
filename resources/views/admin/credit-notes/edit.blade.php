@extends('layouts.admin')

@section('content')


<div class="card">
    <div class="card-header">
        <h4>Edit Credit Note: {{ $creditNote->credit_note_no }}</h4>
        <p class="mb-0">Client: <strong>{{ $creditNote->client->name }}</strong></p>
        <small>Invoice: {{ $creditNote->invoice->invoice_no }} | State: {{ $creditNote->client->client_state }}</small>
    </div>

    <div class="card-body">

        {{-- Display validation errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Display success message --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.credit-notes.update', $creditNote->id) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="invoice_id" value="{{ $creditNote->invoice_id }}">
            <input type="hidden" name="client_id" value="{{ $creditNote->client_id }}">

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Item</th>
                        <th>Invoiced Qty</th>
                        <th>Credit Qty</th>
                        <th>Unit Price</th>
                        <th>Line Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($creditNote->invoice->items as $index => $invoiceItem)
                        @php
                            $creditedItem = $creditNote->items->firstWhere('inventory_item_id', $invoiceItem->inventory_item_id);
                            $creditQty = $creditedItem->quantity ?? 0;
                        @endphp
                        <tr>
                            <td class="text-center">
                                <input type="checkbox" class="item-check" name="items[{{ $index }}][selected]" value="1"
                                    {{ $creditQty > 0 ? 'checked' : '' }}>
                            </td>
                            <td>{{ $invoiceItem->inventoryItem->name }}</td>
                            <td>{{ $invoiceItem->quantity }}</td>
                            <td>
                                <input type="number" name="items[{{ $index }}][qty]" class="form-control credit-qty"
                                    value="{{ $creditQty }}" min="0" max="{{ $invoiceItem->quantity }}">
                                <input type="hidden" name="items[{{ $index }}][id]" value="{{ $invoiceItem->inventory_item_id }}">
                            </td>
                            <td>
                                <input type="text" class="form-control" value="{{ $invoiceItem->unit_price }}" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control line-total" readonly>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="row mt-3">
                <div class="col-md-4 offset-md-8">
                    <div class="mb-2">
                        <label>Discount</label>
                        <input type="number" name="discount" id="discount" class="form-control" value="{{ $creditNote->discount }}" step="0.01">
                    </div>
                    <div class="mb-2">
                        <label>Grand Total</label>
                        <input type="text" id="grandTotal" class="form-control" readonly>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <label>Reason <span class="text-danger">*</span></label>
                <textarea name="reason" class="form-control" required>{{ $creditNote->reason }}</textarea>
            </div>

            <div class="mt-3 text-end">
                <button type="submit" class="btn btn-primary">Update Credit Note</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function calculateTotals() {
    let subtotal = 0;

    document.querySelectorAll('tbody tr').forEach(row => {
        const checked = row.querySelector('.item-check')?.checked;
        if (!checked) {
            row.querySelector('.line-total').value = '0.00';
            return;
        }

        const qty = parseFloat(row.querySelector('.credit-qty').value || 0);
        const price = parseFloat(row.querySelector('td:nth-child(5) input').value || 0);
        const total = qty * price;

        row.querySelector('.line-total').value = total.toFixed(2);
        subtotal += total;
    });

    const discount = parseFloat(document.getElementById('discount').value || 0);
    const grandTotal = Math.max(subtotal - discount, 0);
    document.getElementById('grandTotal').value = grandTotal.toFixed(2);
}

// Recalculate on input or checkbox change
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('credit-qty') || e.target.id === 'discount') {
        calculateTotals();
    }
});
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('item-check')) {
        calculateTotals();
    }
});

calculateTotals();
</script>
@endpush
