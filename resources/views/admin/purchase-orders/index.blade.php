@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Purchase Orders</h4>
        <div class="action_area">
            <a href="{{ route('admin.purchase-orders.create') }}" class="btn ms-auto">Create PO</a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Purchase Orders</li>
        </ol>
    </nav>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif



<table class="data_table">
    <thead>
        <tr>
            <th>PO Number</th>
            <th>Vendor</th>
            <th>Order Date</th>
            <th>Status</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
    <thead>
    <tbody>
        @foreach($orders as $order)
        <tr>
            <td>{{ $order->po_number }}</td>
            <td>{{ $order->vendor->user->name }}</td>
            <td>{{ $order->status }}</td>
            <td>{{ $order->order_date }}</td>
            <td>{{ $order->total_amount }}</td>
            <td>
                @if($order->status == 'draft')
                    <a class="btn bg-success border-success text-light" href="{{ route('admin.purchase-orders.approve', $order->id) }}">
                        Approve
                    </a>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>



@endsection