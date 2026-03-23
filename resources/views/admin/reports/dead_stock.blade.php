@extends('layouts.admin')

@section('content')

<div class="container-fluid">
    <h4 class="mb-4">📦 Dead Stock Report (No Sales in 90+ Days)</h4>

    @if(count($dead) > 0)

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-warning">
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Current Stock</th>
                        <th>Last Updated</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dead as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->name }}</td>
                        <td class="fw-bold">{{ $item->current_stock }}</td>
                        <td>{{ $item->updated_at }}</td>
                        <td>
                            <span class="badge bg-warning text-dark">
                                Dead Stock
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
            🎉 No dead stock items found.
        </div>
    @endif
</div>

@endsection
