@extends('layouts.admin')

@section('content')

<div class="container-fluid">
    <h4 class="mb-4">⚠ Low Stock Report</h4>

    @if($items->count() > 0)

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead class="table-danger">
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Current Stock</th>
                        <th>Minimum Stock</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->name }}</td>
                        <td class="text-danger fw-bold">
                            {{ $item->stocks }}
                        </td>
                        <td>{{ $item->minimum_stock }}</td>
                        <td>
                            <span class="badge bg-danger">
                                Reorder Required
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @else
        <div class="alert alert-success">
            🎉 All inventory items are sufficiently stocked.
        </div>
    @endif
</div>

@endsection
