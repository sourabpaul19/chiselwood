@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Create Purchase Order</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.purchase-orders.index') }}">Purchase Orders</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create Purchase Order</li>
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

<form method="POST" action="{{ route('admin.purchase-orders.store') }}">
@csrf

<div class="postbox">
    <div class="postbox_header">
        <h3>Create Invoice</h3>
        <a href="javascript:void(0)" class="postbox_toggle">
            <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
        </a>
    </div>
    <div class="postbox_body">
        <div class="row mb-3">
            <div class="col-md-auto">
                <div class="form_group">
                    <label class="form-label">Vendor</label><br/>
                    <select name="vendor_id" id="vendor" class="select" required>
                        <option value="">Select Vendor</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}">{{ $vendor->user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <table class="data_table" id="itemsTable">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>HSN</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>GST%</th>
                    <th>Taxable</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th>IGST</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
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
                        <th>Taxable</th>
                        <td><input type="number" name="taxable" id="taxable" class="textbox w-100" readonly></td>
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
                        <th>Grand Total</th>
                        <td><input type="number" name="grandTotal" id="grandTotal" class="textbox w-100" readonly></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card-footer text-end">
            <button type="submit" class="btn btn-theme">Save Purchase Order</button>
        </div>
        
    </div>



{{--
<div class="row justify-content-end">
    <div class="col-md-4">
        <table class="table table-sm">
            <tr><th>Subtotal</th><td id="subtotal">0.00</td></tr>
            <tr><th>Taxable</th><td id="taxable">0.00</td></tr>
            <tr><th>CGST</th><td id="cgst">0.00</td></tr>
            <tr><th>SGST</th><td id="sgst">0.00</td></tr>
            <tr><th>IGST</th><td id="igst">0.00</td></tr>
            <tr class="fw-bold">
                <th>Grand Total</th>
                <td id="grandTotal">0.00</td>
            </tr>
        </table>
    </div>
</div>

<button class="btn btn-success">Save Purchase Order</button>
--}}
</form>
</div>
</div>

@endsection

@push('scripts')
<script>

let vendorItems = [];

$('#vendor').change(function () {
    let vendorId = $(this).val();

    $.get("{{ url('admin/get-vendor-items') }}/" + vendorId, function (data) {
        vendorItems = data;
    });
});

$('#addRow').click(function () {

    if (!vendorItems.length) {
        alert('Select vendor first');
        return;
    }

    let rowIndex = $('#itemsTable tbody tr').length;

    let options = vendorItems.map(item =>
        `<option value="${item.id}"
            data-hsn="${item.sku}"
            data-gst="${item.gst_rate}">
            ${item.name}
        </option>`
    ).join('');

    let row = `
    <tr>
        <td>
            <select name="items[${rowIndex}][item_id]" class="select itemSelect">
                <option value="">Select</option>
                ${options}
            </select>
        </td>
        <td><input type="text" class="textbox w-100 hsn" readonly></td>
        <td><input type="number" name="items[${rowIndex}][quantity]" class="textbox w-100 qty"></td>
        <td><input type="number" name="items[${rowIndex}][unit_price]" class="textbox w-100 rate"></td>
        <td><input type="text" class="textbox w-100 gst" readonly></td>
        <td><input type="text" class="textbox w-100 taxable" readonly></td>
        <td><input type="text" class="textbox w-100 cgst" readonly></td>
        <td><input type="text" class="textbox w-100 sgst" readonly></td>
        <td><input type="text" class="textbox w-100 igst" readonly></td>
        <td><input type="text" class="textbox w-100 total" readonly></td>
        <td><button type="button" class="btn text-danger removeRow">Remove</button></td>
    </tr>`;

    $('#itemsTable tbody').append(row);
});

$(document).on('change', '.itemSelect', function () {

    let selected = $(this).find(':selected');
    let row = $(this).closest('tr');

    row.find('.hsn').val(selected.data('hsn'));
    row.find('.gst').val(selected.data('gst'));

});

$(document).on('keyup change', '.qty, .rate', function () {
    calculate();
});

$(document).on('click', '.removeRow', function () {
    $(this).closest('tr').remove();
    calculate();
});

function calculate() {

    let subtotal = 0;
    let taxableTotal = 0;
    let cgstTotal = 0;
    let sgstTotal = 0;
    let igstTotal = 0;

    $('#itemsTable tbody tr').each(function () {

        let qty = parseFloat($(this).find('.qty').val()) || 0;
        let rate = parseFloat($(this).find('.rate').val()) || 0;
        let gstRate = parseFloat($(this).find('.gst').val()) || 0;

        let lineTotal = qty * rate; // GST INCLUDED TOTAL

        let taxable = 0;
        let gstAmount = 0;

        if (gstRate > 0) {
            taxable = lineTotal / (1 + gstRate / 100);
            gstAmount = lineTotal - taxable;
        } else {
            taxable = lineTotal;
        }

        let cgst = gstAmount / 2;
        let sgst = gstAmount / 2;
        let igst = 0;

        subtotal += lineTotal;
        taxableTotal += taxable;
        cgstTotal += cgst;
        sgstTotal += sgst;
        igstTotal += igst;

        $(this).find('.taxable').val(taxable.toFixed(2));
        $(this).find('.cgst').val(cgst.toFixed(2));
        $(this).find('.sgst').val(sgst.toFixed(2));
        $(this).find('.igst').val(igst.toFixed(2));
        $(this).find('.total').val(lineTotal.toFixed(2));
    });

    $('#subtotal').val(subtotal.toFixed(2));
    $('#taxable').val(taxableTotal.toFixed(2));
    $('#cgst').val(cgstTotal.toFixed(2));
    $('#sgst').val(sgstTotal.toFixed(2));
    $('#igst').val(igstTotal.toFixed(2));
    $('#grandTotal').val(subtotal.toFixed(2)); // Total remains same
}


</script>
@endpush
