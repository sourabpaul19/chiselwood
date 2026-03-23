@extends('layouts.admin')

@section('content')


<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Goods Receipt #{{ $receipt->receipt_number }}</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.purchase-receipts.index') }}">Goods Receipt</a></li>
            <li class="breadcrumb-item active" aria-current="page">Goods Receipt #{{ $receipt->receipt_number }}</li>
        </ol>
    </nav>
</div>


    <div class="postbox mb-3">
        <div class="postbox_body">

            <div class="row">
                <div class="col-md-4">
                    <strong>Receipt No:</strong><br>
                    {{ $receipt->receipt_number }}
                </div>

                <div class="col-md-4">
                    <strong>Receipt Date:</strong><br>
                    {{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d-m-Y') }}
                </div>

                <div class="col-md-4">
                    <strong>Purchase Order:</strong><br>
                    {{ $receipt->purchaseOrder->po_number ?? '-' }}
                </div>

                <div class="col-md-4 mt-3">
                    <strong>Vendor:</strong><br>
                    {{ $receipt->vendor->user->name ?? '-' }}
                </div>

                <div class="col-md-4 mt-3">
                    <strong>Created By:</strong><br>
                    {{ optional($receipt->creator)->name }}
                </div>
            </div>

        </div>
    </div>

    <table class="data_table">
        <thead>
            <tr>
                <th>#</th>
                <th>Item</th>
                <th>Received Qty</th>
                <th>Unit Cost</th>
                <th>Selling Price</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>

        <tbody>
            @php
                $grandTotal = 0;
            @endphp

            @foreach($receipt->items as $item)
                @php
                    $grandTotal += $item->total;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        {{ $item->inventoryItem->name ?? '-' }}
                    </td>
                    <td>{{ $item->received_quantity }}</td>
                    <td>{{ number_format($item->unit_cost, 2) }}</td>
                    <td>{{ number_format($item->selling_price, 2) }}</td>
                    <td class="text-end">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>

        <tfoot>
            <tr>
                <th colspan="5" class="text-end">Grand Total</th>
                <th class="text-end">{{ number_format($grandTotal, 2) }}</th>
            </tr>
        </tfoot>
    </table>

@endsection
