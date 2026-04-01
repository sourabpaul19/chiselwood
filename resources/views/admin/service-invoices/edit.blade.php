@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Edit Service Invoice #{{ $serviceInvoice->invoice_no }}</h4>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.service-invoices.index') }}">Service Invoices</a></li>
            <li class="breadcrumb-item active">Edit Service Invoice</li>
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

<form action="{{ route('admin.service-invoices.update', $serviceInvoice->id) }}" method="POST">
@csrf
@method('PUT')

<div class="postbox">
    <div class="postbox_header">
        <h3>Edit Service Invoice</h3>
    </div>

    <div class="postbox_body">

        {{-- CLIENT --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Client</label>
                <select name="client_id" id="client_id" class="select" required>
                    <option value="">Select Client</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" data-state="{{ strtolower($client->client_state) }}"
                            {{ $serviceInvoice->client_id == $client->id ? 'selected' : '' }}>
                            {{ $client->company_name ?? $client->user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- SERVICES TABLE --}}
        <table class="data_table" id="serviceTable">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Price (Incl GST)</th>
                    <th>GST %</th>
                    <th>Taxable</th>
                    <th>CGST</th>
                    <th>SGST</th>
                    <th>IGST</th>
                    <th>Total</th>
                    <th></th>
                </tr>
            </thead>

            <tbody>
                @foreach($serviceInvoice->items as $index => $item)
                <tr>
                    <td>
                        <input type="text" name="items[{{ $index }}][name]" class="textbox w-100" value="{{ $item->service_name }}" required>
                    </td>
                    <td>
                        <input type="number" name="items[{{ $index }}][price]" class="textbox w-100 price" value="{{ $item->unit_price }}" required>
                    </td>
                    <td>
                        <input type="number" name="items[{{ $index }}][gst_rate]" class="textbox w-100 gst" value="{{ $item->gst_rate ?? 18 }}">
                    </td>
                    <td><input class="textbox w-100 taxable" readonly value="{{ $item->taxable_amount }}"></td>
                    <td><input class="textbox w-100 cgst" readonly value="{{ $item->cgst }}"></td>
                    <td><input class="textbox w-100 sgst" readonly value="{{ $item->sgst }}"></td>
                    <td><input class="textbox w-100 igst" readonly value="{{ $item->igst }}"></td>
                    <td><input class="textbox w-100 total" readonly value="{{ $item->total_price }}"></td>
                    <td>
                        <button type="button" class="btn text-danger removeRow">Remove</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button type="button" class="btn mt-3" id="addRow">+ Add Service</button>

        <hr>

        {{-- TOTALS --}}
        <div class="row">
            <div class="col-md-4 offset-md-8">
                <table class="table">
                    <tr>
                        <th>Subtotal</th>
                        <td><input id="subtotal" class="textbox w-100" readonly value="{{ $serviceInvoice->subtotal }}"></td>
                    </tr>
                    <tr>
                        <th>CGST</th>
                        <td><input id="cgst_total" class="textbox w-100" readonly value="{{ $serviceInvoice->cgst }}"></td>
                    </tr>
                    <tr>
                        <th>SGST</th>
                        <td><input id="sgst_total" class="textbox w-100" readonly value="{{ $serviceInvoice->sgst }}"></td>
                    </tr>
                    <tr>
                        <th>IGST</th>
                        <td><input id="igst_total" class="textbox w-100" readonly value="{{ $serviceInvoice->igst }}"></td>
                    </tr>
                    <tr>
                        <th>Discount</th>
                        <td><input name="discount" id="discount" class="textbox w-100" value="{{ $serviceInvoice->discount }}"></td>
                    </tr>
                    <tr>
                        <th>Grand Total</th>
                        <td><input id="grand_total" name="grand_total" class="textbox w-100" readonly value="{{ $serviceInvoice->grand_total }}"></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="text-end">
            <button class="btn btn-theme">Update Invoice</button>
        </div>

    </div>
</div>

</form>

@endsection

@push('scripts')
<script>
let rowIndex = {{ $serviceInvoice->items->count() - 1 }};
const COMPANY_STATE = 'wb';

function recalc() {
    let subtotal = 0, cgst = 0, sgst = 0, igst = 0;
    let clientState = ($('#client_id option:selected').data('state') || '').toLowerCase();

    $('#serviceTable tbody tr').each(function(){
        let price = parseFloat($(this).find('.price').val()) || 0;
        let gst   = parseFloat($(this).find('.gst').val()) || 0;

        let taxable = gst > 0 ? (price * 100) / (100 + gst) : price;
        let gstAmount = price - taxable;

        let c = 0, s = 0, i = 0;
        if(clientState === COMPANY_STATE){
            c = gstAmount / 2;
            s = gstAmount / 2;
        } else {
            i = gstAmount;
        }

        let total = price;

        $(this).find('.taxable').val(taxable.toFixed(2));
        $(this).find('.cgst').val(c.toFixed(2));
        $(this).find('.sgst').val(s.toFixed(2));
        $(this).find('.igst').val(i.toFixed(2));
        $(this).find('.total').val(total.toFixed(2));

        subtotal += taxable;
        cgst += c;
        sgst += s;
        igst += i;
    });

    let discount = parseFloat($('#discount').val()) || 0;
    let grand = Math.max((subtotal + cgst + sgst + igst) - discount, 0);

    $('#subtotal').val(subtotal.toFixed(2));
    $('#cgst_total').val(cgst.toFixed(2));
    $('#sgst_total').val(sgst.toFixed(2));
    $('#igst_total').val(igst.toFixed(2));
    $('#grand_total').val(grand.toFixed(2));
}

// EVENTS
$(document).on('input','.price,.gst,#discount',recalc);
$('#client_id').on('change',recalc);

// ADD ROW
$('#addRow').click(function(){
    rowIndex++;
    $('#serviceTable tbody').append(`
        <tr>
            <td><input type="text" name="items[${rowIndex}][name]" class="textbox w-100" required></td>
            <td><input type="number" name="items[${rowIndex}][price]" class="textbox w-100 price"></td>
            <td><input type="number" name="items[${rowIndex}][gst_rate]" class="textbox w-100 gst" value="18"></td>
            <td><input class="textbox w-100 taxable" readonly></td>
            <td><input class="textbox w-100 cgst" readonly></td>
            <td><input class="textbox w-100 sgst" readonly></td>
            <td><input class="textbox w-100 igst" readonly></td>
            <td><input class="textbox w-100 total" readonly></td>
            <td><button type="button" class="btn text-danger removeRow">Remove</button></td>
        </tr>
    `);
});

// REMOVE ROW
$(document).on('click','.removeRow',function(){
    if($('#serviceTable tbody tr').length > 1){
        $(this).closest('tr').remove();
        recalc();
    }
});

// Initial calculation
recalc();
</script>
@endpush