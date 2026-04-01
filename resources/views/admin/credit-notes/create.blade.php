@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Issue Credit Note (Invoice: {{ $invoice->invoice_no }})</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Issue Credit Note (Invoice: {{ $invoice->invoice_no }})</li>
        </ol>
    </nav>
</div>


<form method="POST" action="{{ route('admin.credit-notes.store') }}">
    @csrf
    <input type="hidden" name="invoice_id" value="{{ $invoice->id }}">
    <input type="hidden" name="client_id" value="{{ $invoice->client_id }}">

        <table class="data_table">
            <thead>
                <tr>
                    <th></th>
                    <th>Item</th>
                    <th>Invoiced Qty</th>
                    <th>Credit Qty</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
@foreach ($invoice->items as $index => $item)
<tr>
    <td><input type="checkbox"
               name="items[{{ $index }}][selected]"
               value="1"
               class="item-check"
               checked></td>
    <td>
        

        {{ $item->inventoryItem->name }}
    </td>

    <td>{{ $item->quantity }}</td>

    <td>
        <input type="number"
               name="items[{{ $index }}][qty]"
               class="textbox w-100 credit-qty"
               value="1"
               min="1"
               max="{{ $item->quantity }}">
    </td>

    <td>
        <input type="number"
               name="items[{{ $index }}][price]"
               class="textbox w-100 price"
               value="{{ $item->unit_price }}"
               step="0.01">
    </td>

    <td>
        <input type="hidden"
               name="items[{{ $index }}][id]"
               value="{{ $item->inventory_item_id }}">

        <input type="text"
               class="textbox w-100 line-total"
               readonly>
    </td>
</tr>
@endforeach
</tbody>

        </table>

        <div class="row mt-3">
            <div class="col-md-4 offset-md-8">
                <div class="mb-2">
                    <label>Discount</label>
                    <input type="number" name="discount" id="discount" class="form-control" value="0" step="0.01">
                </div>
                <div class="mb-2">
                    <label>Grand Total</label>
                    <input type="text" id="grandTotal" class="form-control" readonly>
                </div>
            </div>
        </div>

        <div class="mt-3">
            <label>Reason <span class="text-danger">*</span></label>
            <textarea name="reason" class="form-control" required></textarea>
        </div>

    <div class="card-footer text-end">
        <button type="submit" class="btn btn-theme">Issue Credit Note</button>
    </div>
</form>

@endsection

@push('scripts')

<script>
function calculateTotals() {
    let subtotal = 0;

    document.querySelectorAll('tbody tr').forEach(row => {

        const checkbox   = row.querySelector('.item-check');
        const qtyInput   = row.querySelector('.credit-qty');
        const priceInput = row.querySelector('.price');
        const lineTotal  = row.querySelector('.line-total');

        const checked = checkbox?.checked;

        if (!checked) {
            if (lineTotal) lineTotal.value = '0.00';
            if (qtyInput) qtyInput.disabled = true;
            if (priceInput) priceInput.disabled = true;
            return;
        }

        if (qtyInput) qtyInput.disabled = false;
        if (priceInput) priceInput.disabled = false;

        const qty   = parseFloat(qtyInput?.value || 0);
        const price = parseFloat(priceInput?.value || 0);
        const total = qty * price;

        if (lineTotal) lineTotal.value = total.toFixed(2);
        subtotal += total;
    });

    const discount = parseFloat(document.getElementById('discount')?.value || 0);
    const grandTotal = Math.max(subtotal - discount, 0);

    document.getElementById('grandTotal').value = grandTotal.toFixed(2);
}

document.addEventListener('input', function (e) {
    if (
        e.target.classList.contains('credit-qty') ||
        e.target.classList.contains('price') ||
        e.target.id === 'discount'
    ) {
        calculateTotals();
    }
});

document.addEventListener('change', function (e) {
    if (e.target.classList.contains('item-check')) {
        calculateTotals();
    }
});

calculateTotals();
</script>


@endpush
