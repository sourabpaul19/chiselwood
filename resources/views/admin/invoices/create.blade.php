@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Add Invoice</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.invoices.index') }}">Invoices</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Invoice</li>
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


<form action="{{ route('admin.invoices.store') }}" method="POST">
        @csrf
<div class="postbox">
    <div class="postbox_header">
        <h3>Create Invoice</h3>
        <a href="javascript:void(0)" class="postbox_toggle">
            <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
        </a>
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
                                        data-state="{{ $client->client_state }}">
                                    {{ $client->company_name ?? $client->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- ITEMS --}}
            <table class="data_table" id="itemsTable">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Stock</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Discount Type</th>
                        <th>Discount</th>
                        <th>Taxable Amount</th>
                        <th>GST %</th>
                        <th>CGST</th>
                        <th>SGST</th>
                        <th>IGST</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>
                            <select name="items[0][id]" class="textbox w-100 item-select" required>
                                <option value="">Select Item</option>
                                @foreach($items as $item)
                                    <option value="{{ $item->id }}"
                                        data-price="{{ $item->selling_price }}"
                                        data-stock="{{ $item->stocks }}"
                                        data-gst="{{ $item->gst_rate }}"
                                        data-discount-type="{{ $item->discount_type }}"
                                        data-discount-value="{{ $item->discount_value }}">
                                        {{ $item->name }} ({{ $item->sku }})
                                    </option>
                                @endforeach
                            </select>
                        </td>

                        <td class="stock text-center">0</td>
                        <td><input type="number" name="items[0][qty]" class="textbox w-100 qty" min="1" value="1" required></td>
                        <td><input type="number" name="items[0][price]" class="textbox w-100 price" readonly></td>
                        <td>
                            <select class="textbox w-100 discount-type" name="items[0][discount_type]">
                                <option value="percent">%</option>
                                <option value="flat">Flat</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" class="textbox w-100 discount-value" name="items[0][discount_value]" min="0" step="0.01" max="100" value="0">
                        </td>
                        <td>
                            <input type="text" class="textbox w-100 item-subtotal" readonly value="0.00">
                        </td>
                        <td><input type="number" name="items[0][gst_rate]" class="textbox w-100 gst_rate" readonly></td>
                        <td><input type="number" name="items[0][cgst]" class="textbox w-100 cgst" readonly></td>
                        <td><input type="number" name="items[0][sgst]" class="textbox w-100 sgst" readonly></td>
                        <td><input type="number" name="items[0][igst]" class="textbox w-100 igst" readonly></td>
                        <td><input type="number" class="textbox w-100 total" readonly></td>
                        <td><button type="button" class="btn text-danger removeRow">Remove</button></td>
                    </tr>
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
                            <td><input type="number" name="subtotal" id="subtotal" class="textbox w-100" readonly></td>
                        </tr>
                        <tr>
                            <th>CGST Total</th>
                            <td><input type="number" name="cgst" id="cgst" class="textbox w-100" readonly></td>
                        </tr>
                        <tr>
                            <th>SGST Total</th>
                            <td><input type="number" name="sgst" id="sgst" class="textbox w-100" readonly></td>
                        </tr>
                        <tr>
                            <th>IGST Total</th>
                            <td><input type="number" name="igst" id="igst" class="textbox w-100" readonly></td>
                        </tr>
                        <tr>
                            <th>Discount</th>
                            <td><input type="number" name="discount" id="discount" class="textbox w-100" value="0"></td>
                        </tr>
                        <tr>
                            <th>Grand Total</th>
                            <td><input type="number" name="grand_total" id="grand_total" class="textbox w-100" readonly></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card-footer text-end">
                <button type="submit" class="btn btn-theme">Save Invoice</button>
            </div>

        </div>

        
    
    </div>
</form>

@endsection

@push('scripts')
<script>
let rowIndex = 0;
const COMPANY_STATE = 'WB';

// =========================
// Recalculate totals
// =========================
function recalc() {
    let subtotal = 0, totalCgst = 0, totalSgst = 0, totalIgst = 0;

    let clientState  = ($('#client_id option:selected').data('state') || '').toString().trim().toLowerCase();
    let companyState = COMPANY_STATE.toLowerCase();

    $('#itemsTable tbody tr').each(function () {

        let qty     = parseFloat($(this).find('.qty').val()) || 0;
        let price   = parseFloat($(this).find('.price').val()) || 0; // GST INCLUDED
        let gstRate = parseFloat($(this).find('.gst_rate').val()) || 0;

        let discountType  = $(this).find('.discount-type').val();
        let discountValue = parseFloat($(this).find('.discount-value').val()) || 0;

        /* ===============================
           GROSS (INCLUSIVE)
        =============================== */
        let gross = qty * price;

        /* ===============================
           ITEM DISCOUNT
        =============================== */
        let discountAmount = discountType === 'percent'
            ? gross * discountValue / 100
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
            $(this).find('.igst').val('0.00');
        } else {
            igst = gstAmount;
            $(this).find('.cgst').val('0.00');
            $(this).find('.sgst').val('0.00');
        }

        /* ===============================
           SET VALUES
        =============================== */
        $(this).find('.item-subtotal').val(taxable.toFixed(2));
        $(this).find('.cgst').val(cgst.toFixed(2));
        $(this).find('.sgst').val(sgst.toFixed(2));
        $(this).find('.igst').val(igst.toFixed(2));

        // Inclusive total
        $(this).find('.total').val(grossAfterDiscount.toFixed(2));

        subtotal  += taxable;
        totalCgst += cgst;
        totalSgst += sgst;
        totalIgst += igst;
    });

    /* ===============================
       INVOICE TOTAL
    =============================== */
    let invoiceDiscount = parseFloat($('#discount').val()) || 0;

    let grandTotal = Math.max(
        (subtotal + totalCgst + totalSgst + totalIgst) - invoiceDiscount,
        0
    );

    $('#subtotal').val(subtotal.toFixed(2));
    $('#cgst').val(totalCgst.toFixed(2));
    $('#sgst').val(totalSgst.toFixed(2));
    $('#igst').val(totalIgst.toFixed(2));
    $('#grand_total').val(grandTotal.toFixed(2));
}

// =========================
// Event Listeners
// =========================
$('#client_id').on('change', recalc);

// Item select change
$(document).on('change', '.item-select', function () {
    let row = $(this).closest('tr');
    let selected = $(this).find(':selected');

    let price = parseFloat(selected.data('price')) || 0;
    let gst = parseFloat(selected.data('gst')) || 0;
    let stock = parseInt(selected.data('stock')) || 0;

    let discountType = selected.data('discount-type') || 'percent';
    let discountValue = parseFloat(selected.data('discount-value')) || 0;

    row.find('.price').val(price);
    row.find('.gst_rate').val(gst);
    row.find('.stock').text(stock);
    row.find('.qty').val(1);

    // ✅ Load default discount
    row.find('.discount-type').val(discountType);
    row.find('.discount-value').val(discountValue);

    recalc();
});

// Quantity / Discount input change
$(document).on('input', '.qty, .discount-type, .discount-value', recalc);

// Invoice-level discount change
$('#discount').on('input', recalc);

// Add row
$('#addRow').click(function () {
    rowIndex++;
    let row = `
    <tr>
        <td>
            <select name="items[${rowIndex}][id]" class="textbox w-100 item-select" required>
                <option value="">Select Item</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}"
                        data-price="{{ $item->selling_price }}"
                        data-stock="{{ $item->stocks }}"
                        data-gst="{{ $item->gst_rate }}"
                        data-discount-type="{{ $item->discount_type }}"
                        data-discount-value="{{ $item->discount_value }}">
                        {{ $item->name }} ({{ $item->sku }})
                    </option>
                @endforeach
            </select>
        </td>
        <td class="stock text-center">0</td>
        <td><input type="number" name="items[${rowIndex}][qty]" class="textbox w-100 qty" min="1" value="1" required></td>
        <td><input type="number" name="items[${rowIndex}][price]" class="textbox w-100 price" readonly></td>
        <td>
            <select class="textbox w-100 discount-type" name="items[${rowIndex}][discount_type]">
                <option value="percent">%</option>
                <option value="flat">Flat</option>
            </select>
        </td>
        <td>
            <input type="number" class="textbox w-100 discount-value" name="items[${rowIndex}][discount_value]" min="0" step="0.01" max="100" value="0">
        </td>
        <td>
            <input type="text" class="textbox w-100 item-subtotal" readonly value="0.00">
        </td>
        <td><input type="number" name="items[${rowIndex}][gst_rate]" class="textbox w-100 gst_rate" readonly></td>
        <td><input type="number" name="items[${rowIndex}][cgst]" class="textbox w-100 cgst" readonly></td>
        <td><input type="number" name="items[${rowIndex}][sgst]" class="textbox w-100 sgst" readonly></td>
        <td><input type="number" name="items[${rowIndex}][igst]" class="textbox w-100 igst" readonly></td>
        <td><input type="number" class="textbox w-100 total" readonly></td>
        <td><button type="button" class="btn text-danger removeRow">Remove</button></td>
    </tr>`;
    $('#itemsTable tbody').append(row);
});

// Remove row
$(document).on('click', '.removeRow', function () {
    if ($('#itemsTable tbody tr').length > 1) $(this).closest('tr').remove();
    recalc();
});
</script>
@endpush
