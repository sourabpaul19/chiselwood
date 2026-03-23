@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Create Purchase Receipt (GRN)</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.purchase-receipts.index') }}">Purchase Receipt (GRN)</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create Purchase Receipt (GRN)</li>
        </ol>
    </nav>
</div>


    <form method="POST" action="{{ route('admin.purchase-receipts.store') }}">
        @csrf
        <div class="postbox">
    <div class="postbox_header">
        <h3>GRN Information</h3>
        <a href="javascript:void(0)" class="postbox_toggle">
            <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
        </a>
    </div>
    <div class="postbox_body">

        {{-- ============================= --}}
        {{-- PURCHASE ORDER SELECT --}}
        {{-- ============================= --}}
        <div class="row mb-3">
            <div class="col-md-auto">
                <div class="form_group">
                    <label class="form-label">Select Purchase Order</label><br/>
                    <select name="purchase_order_id" id="po_select" class="select" required>
                        <option value="">-- Select PO --</option>

                        @foreach($orders as $order)
                            <option value="{{ $order->id }}"
                                data-items='@json($order->items)'>
                                PO-{{ $order->id }}
                                | Vendor: {{ $order->vendor ? $order->vendor->user->name : 'N/A' }}
                            </option>
                        @endforeach

                    </select>
                </div>
            </div>
        </div>

    
        {{-- ============================= --}}
        {{-- ITEMS TABLE --}}
        {{-- ============================= --}}


                <table class="data_table" id="items_table">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Ordered</th>
                            <th>Received</th>
                            <th>Pending</th>
                            <th>Purchase Price</th>
                            <th>Selling Price</th>
                            <th width="120">Receive Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                Select Purchase Order First
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="text-end mt-3">
                    <button class="btn btn-theme">
                        Save Goods Receipt
                    </button>
                </div>
</div>
</div>

    </form>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    let poSelect = document.getElementById('po_select');

    if (!poSelect) {
        console.log('PO select not found');
        return;
    }

    poSelect.addEventListener('change', function() {

        let selectedOption = this.options[this.selectedIndex];
        let items = selectedOption.getAttribute('data-items');
        let tableBody = document.querySelector('#items_table tbody');

        tableBody.innerHTML = '';

        if (!items) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        No Items Found
                    </td>
                </tr>`;
            return;
        }

        try {
            items = JSON.parse(items);
        } catch (e) {
            console.log('JSON parse error', e);
            return;
        }

        let rowIndex = 0;

        items.forEach(function(item) {

            let ordered  = parseFloat(item.quantity);
            let received = parseFloat(item.received_quantity);
            let pending  = ordered - received;

            if (pending <= 0) return;

            tableBody.innerHTML += `
                <tr>
                    <td>
                        ${item.inventory_item.name}
                        <input type="hidden" name="items[${rowIndex}][po_item_id]" value="${item.id}">
                    </td>
                    <td>${ordered}</td>
                    <td>${received}</td>
                    <td>${pending}</td>
                    <td>
                        <input type="number"
                            name="items[${rowIndex}][unit_price]"
                            class="textbox w-100"
                            value="${item.unit_price}"
                            step="0.01"
                            readonly>
                    </td>
                    <td>
                        <input type="number"
                            name="items[${rowIndex}][selling_price]"
                            class="textbox w-100"
                            step="0.01">
                    </td>
                    <td>
                        <input type="number"
                            name="items[${rowIndex}][received_qty]"
                            class="textbox w-100"
                            min="0"
                            max="${pending}"
                            step="0.01">
                    </td>
                </tr>
            `;

            rowIndex++;
        });

    });

});
</script>

@endpush
