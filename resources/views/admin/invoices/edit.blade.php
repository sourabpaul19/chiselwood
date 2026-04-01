@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Edit Invoice - {{ $invoice->invoice_no }}</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.invoices.index') }}">Invoices</a></li>
            <li class="breadcrumb-item active" aria-current="page">Edit Invoice - {{ $invoice->invoice_no }}</li>
        </ol>
    </nav>
</div>
<form action="{{ route('admin.invoices.update', $invoice->id) }}" method="POST">
        @csrf
        @method('PUT')
<div class="postbox">
    <div class="postbox_header">
        <h3>Edit Invoice - {{ $invoice->invoice_no }}</h3>
    </div>

    

        <div class="postbox_body">

            {{-- CLIENT --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form_group">
                        <label class="form-label">Client</label><br/>
                        <select name="client_id" id="client_id" class="select" required>
                            <option value="">Select Client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                        data-state="{{ $client->client_state }}"
                                        {{ $invoice->client_id == $client->id ? 'selected' : '' }}>
                                    {{ $client->company_name ?? $client->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- ITEMS --}}
            <table class="data_table" id="itemsTable">
                <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th>Stock</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Discount Type</th>
                        <th>Discount</th>
                        <th>Item Subtotal</th>
                        <th>GST %</th>
                        <th>CGST</th>
                        <th>SGST</th>
                        <th>IGST</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($invoice->items as $index => $row)
                    <tr data-discount-overridden="true">
                        <td>
                            <select name="items[{{ $index }}][id]" class="textbox w-100 item-select" required>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}"
                                        data-price="{{ $item->selling_price }}"
                                        data-stock="{{ $item->current_stock }}"
                                        data-gst="{{ $item->gst_rate }}"
                                        data-discount-type="{{ $item->discount_type }}"
                                        data-discount-value="{{ $item->discount_value }}"
                                        {{ $item->id == $row->inventory_item_id ? 'selected' : '' }}>
                                        {{ $item->name }} ({{ $item->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <td class="stock text-center">
                            {{ $row->inventoryItem->current_stock }}
                        </td>

                        <td>
                            <input type="number"
                                name="items[{{ $index }}][qty]"
                                class="textbox w-100 qty"
                                value="{{ $row->quantity }}"
                                min="1">
                        </td>

                        <td>
                            <input type="number"
                                name="items[{{ $index }}][price]"
                                class="textbox w-100 price"
                                value="{{ $row->unit_price }}"
                                readonly>
                        </td>

                        <td>
                            <select name="items[{{ $index }}][discount_type]"
                                    class="textbox w-100 discount-type">
                                <option value="percent" {{ $row->discount_type === 'percent' ? 'selected' : '' }}>%</option>
                                <option value="flat" {{ $row->discount_type === 'flat' ? 'selected' : '' }}>Flat</option>
                            </select>
                        </td>

                        <td>
                            <input type="number"
                                name="items[{{ $index }}][discount_value]"
                                class="textbox w-100 discount-value"
                                value="{{ $row->discount_value }}"
                                min="0" step="0.01" max="100">
                        </td>

                        <td>
                            <input type="text"
                                class="textbox w-100 item-subtotal"
                                value="{{ number_format($row->item_subtotal, 2) }}"
                                readonly>
                        </td>

                        <td>
                            <input type="number"
                                class="textbox w-100 gst_rate"
                                value="{{ $row->gst_rate }}"
                                readonly>
                        </td>

                        <td><input class="textbox w-100 cgst" value="{{ number_format($row->cgst, 2) }}" readonly></td>
                        <td><input class="textbox w-100 sgst" value="{{ number_format($row->sgst, 2) }}" readonly></td>
                        <td><input class="textbox w-100 igst" value="{{ number_format($row->igst, 2) }}" readonly></td>

                        <td>
                            <input class="textbox w-100 total"
                                value="{{ number_format($row->total_price, 2) }}"
                                readonly>
                        </td>

                        <td>
                            <button type="button" class="btn text-danger removeRow">Remove</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <button type="button" class="btn mt-3" id="addRow">
                + Add Item
            </button>

            <hr>

            {{-- TOTALS --}}
            <div class="row">
                <div class="col-md-4 offset-md-8">
                    <table class="table">
                        <tr>
                            <th>Subtotal</th>
                            <td><input type="number" name="subtotal" id="subtotal" class="textbox w-100" value="{{ $invoice->subtotal }}" readonly></td>
                        </tr>
                        <tr>
                            <th>CGST Total</th>
                            <td><input type="number" name="cgst" id="cgst" class="textbox w-100" value="{{ $invoice->cgst }}" readonly></td>
                        </tr>
                        <tr>
                            <th>SGST Total</th>
                            <td><input type="number" name="sgst" id="sgst" class="textbox w-100" value="{{ $invoice->sgst }}" readonly></td>
                        </tr>
                        <tr>
                            <th>IGST Total</th>
                            <td><input type="number" name="igst" id="igst" class="textbox w-100" value="{{ $invoice->igst }}" readonly></td>
                        </tr>
                        <tr>
                            <th>Discount</th>
                            <td><input type="number" name="discount" id="discount" class="textbox w-100" value="{{ $invoice->discount }}"></td>
                        </tr>
                        <tr>
                            <th>Grand Total</th>
                            <td><input type="number" name="grand_total" id="grand_total" class="textbox w-100" value="{{ $invoice->grand_total }}" readonly></td>
                        </tr>
                    </table>
                </div>
            </div>
<div class="card-footer text-end">
            <button type="submit" class="btn btn-theme">Update Invoice</button>
        </div>
        </div>

        
    
</div>
</form>
@endsection

@push('scripts')
<script>
let rowIndex = {{ $invoice->items->count() - 1 }};
const COMPANY_STATE = 'WB';

function recalc() {

    let subtotal = 0, totalCgst = 0, totalSgst = 0, totalIgst = 0;

    let clientState  = ($('#client_id option:selected').data('state') || '').toLowerCase().trim();
    let companyState = COMPANY_STATE.toLowerCase();

    $('#itemsTable tbody tr').each(function () {

        let qty     = parseFloat($(this).find('.qty').val()) || 0;
        let price   = parseFloat($(this).find('.price').val()) || 0; // GST INCLUDED
        let gstRate = parseFloat($(this).find('.gst_rate').val()) || 0;

        let discountType  = $(this).find('.discount-type').val();
        let discountValue = parseFloat($(this).find('.discount-value').val()) || 0;

        /* ===============================
        GROSS
        =============================== */
        let gross = qty * price;

        /* ===============================
        ITEM DISCOUNT
        =============================== */
        let discountAmount =
            discountType === 'percent'
                ? (gross * discountValue / 100)
                : discountValue;

        discountAmount = Math.min(discountAmount, gross);

        let grossAfterDiscount = gross - discountAmount;

        /* ===============================
        GST INCLUSIVE EXTRACTION
        =============================== */
        let taxable = gstRate > 0
            ? (grossAfterDiscount * 100) / (100 + gstRate)
            : grossAfterDiscount;

        let gstAmount = grossAfterDiscount - taxable;

        let cgst = 0, sgst = 0, igst = 0;

        if (clientState === companyState) {
            cgst = gstAmount / 2;
            sgst = gstAmount / 2;
        } else {
            igst = gstAmount;
        }

        /* ===============================
        SET ROW VALUES (DISPLAY ONLY)
        =============================== */
        // $(this).find('.item-subtotal').val(taxable);
        // $(this).find('.cgst').val(cgst);
        // $(this).find('.sgst').val(sgst);
        // $(this).find('.igst').val(igst);
        // $(this).find('.total').val(grossAfterDiscount);

        $(this).find('.item-subtotal').val(taxable.toFixed(2));
        $(this).find('.cgst').val(cgst.toFixed(2));
        $(this).find('.sgst').val(sgst.toFixed(2));
        $(this).find('.igst').val(igst.toFixed(2));

        // Inclusive total
        $(this).find('.total').val(grossAfterDiscount.toFixed(2));

        /* ===============================
        TOTALS
        =============================== */
        subtotal  += taxable;
        totalCgst += cgst;
        totalSgst += sgst;
        totalIgst += igst;
    });

    /* ===============================
    INVOICE DISCOUNT (AFTER TAX)
    =============================== */
    let invoiceDiscount = parseFloat($('#discount').val()) || 0;

    let grandTotal = Math.max(
        (subtotal + totalCgst + totalSgst + totalIgst) - invoiceDiscount,
        0
    );

    /* ===============================
    SET TOTAL FIELDS
    =============================== */
    // $('#subtotal').val(subtotal);
    // $('#cgst').val(totalCgst);
    // $('#sgst').val(totalSgst);
    // $('#igst').val(totalIgst);
    // $('#grand_total').val(grandTotal);

    $('#subtotal').val(subtotal.toFixed(2));
    $('#cgst').val(totalCgst.toFixed(2));
    $('#sgst').val(totalSgst.toFixed(2));
    $('#igst').val(totalIgst.toFixed(2));
    $('#grand_total').val(grandTotal.toFixed(2));
}

/* ===============================
INIT
=============================== */
$(document).ready(function () {
    $('#itemsTable tbody tr').each(function () {
        $(this).data('discount-overridden', true);
    });
    recalc();
});

/* ===============================
EVENTS
=============================== */
$('#client_id').on('change', recalc);

$(document).on('change', '.item-select', function () {

    let row = $(this).closest('tr');
    let opt = $(this).find(':selected');

    row.find('.price').val(opt.data('price') || 0);
    row.find('.gst_rate').val(opt.data('gst') || 0);
    row.find('.stock').text(opt.data('stock') || 0);

    if (!row.data('discount-overridden')) {
        row.find('.discount-type').val(opt.data('discount-type') || 'percent');
        row.find('.discount-value').val(opt.data('discount-value') || 0);
    }

    recalc();
});

$(document).on('input change', '.qty, .price, .discount-value, .discount-type', recalc);
$('#discount').on('input', recalc);

/* ===============================
ADD ROW
=============================== */
$('#addRow').click(function () {
    rowIndex++;

    $('#itemsTable tbody').append(`
        <tr>
            <td>
                <select name="items[${rowIndex}][id]" class="textbox w-100 item-select" required>
                    <option value="">Select Item</option>
                    @foreach($items as $item)
                        <option value="{{ $item->id }}"
                            data-price="{{ $item->selling_price }}"
                            data-stock="{{ $item->current_stock }}"
                            data-gst="{{ $item->gst_rate }}"
                            data-discount-type="{{ $item->discount_type }}"
                            data-discount-value="{{ $item->discount_value }}">
                            {{ $item->name }} ({{ $item->sku }})
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="stock text-center">0</td>
            <td><input type="number" name="items[${rowIndex}][qty]" class="textbox w-100 qty" min="1" value="1"></td>
            <td><input type="number" name="items[${rowIndex}][price]" class="textbox w-100 price" readonly></td>
            <td>
                <select class="textbox w-100 discount-type" name="items[${rowIndex}][discount_type]">
                    <option value="percent">%</option>
                    <option value="flat">Flat</option>
                </select>
            </td>
            <td><input type="number" class="textbox w-100 discount-value" name="items[${rowIndex}][discount_value]" value="0"></td>
            <td><input type="text" class="textbox w-100 item-subtotal" readonly></td>
            <td><input type="number" class="textbox w-100 gst_rate" readonly></td>
            <td><input type="number" class="textbox w-100 cgst" readonly></td>
            <td><input type="number" class="textbox w-100 sgst" readonly></td>
            <td><input type="number" class="textbox w-100 igst" readonly></td>
            <td><input type="number" class="textbox w-100 total" readonly></td>
            <td><button type="button" class="btn text-danger removeRow">Remove</button></td>
        </tr>
    `);
});

/* ===============================
REMOVE ROW
=============================== */
$(document).on('click', '.removeRow', function () {
    if ($('#itemsTable tbody tr').length > 1) {
        $(this).closest('tr').remove();
        recalc();
    }
});
</script>
@endpush
