@extends('layouts.admin')

@section('content')


<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Goods Receipt (GRN)</h4>
        <div class="action_area">
            <a href="{{ route('admin.purchase-receipts.create') }}" class="btn">
                + Create Receipt
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Goods Receipt (GRN)</li>
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
                        <th>#</th>
                        <th>Receipt No</th>
                        <th>Purchase Order</th>
                        <th>Vendor</th>
                        <th>Date</th>
                        <th>Total Items</th>
                        <th>Created By</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($receipts as $receipt)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $receipt->receipt_number }}</td>
                            <td>
                                {{ $receipt->purchaseOrder->po_number ?? '-' }}
                            </td>
                            <td>
                                {{ $receipt->vendor->user->name ?? '-' }}
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($receipt->receipt_date)->format('d-m-Y') }}
                            </td>
                            <td>
                                {{ $receipt->items->count() }}
                            </td>
                            <td>
                                {{ optional($receipt->creator)->name }}
                            </td>
                            <td>
                                <a href="{{ route('admin.purchase-receipts.show', $receipt->id) }}"
                                   class="btn btn-sm btn-info">View</a>

                                <a href="{{ route('admin.purchase-receipts.edit', $receipt->id) }}"
                                   class="btn btn-sm btn-warning">Edit</a>

                                <form action="{{ route('admin.purchase-receipts.destroy', $receipt->id) }}"
                                      method="POST"
                                      style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn text-danger"
                                            onclick="return confirm('Delete this receipt?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                No Goods Receipts Found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

 

        {{ $receipts->links() }}




@endsection
