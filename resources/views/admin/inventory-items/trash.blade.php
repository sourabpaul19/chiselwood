@extends('layouts.admin')

@section('content')
<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Trash Items</h4>
        <div class="action_area">
            <a href="{{ route('admin.inventory-items.create') }}" class="btn ms-auto">Add New Items</a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.inventory-items.index') }}">Items</a></li>
            <li class="breadcrumb-item active">Trash Items</li>
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
            <th>Item Name</th>
            <th>SKU</th>
            <th>Categories</th>
            <th>Sub Categories</th>
            <th>Deleted At</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
    @forelse($items as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->sku }}</td>
            <td>
                @foreach($item->categories as $category)
                    <span class="badge bg-primary">{{ $category->name }}</span>
                @endforeach

            </td>
            <td>
                @foreach($item->subCategories as $sub)
                    <span class="badge bg-info">{{ $sub->name }}</span>
                @endforeach
            </td>
            <td>{{ $item->deleted_at->format('d M Y, h:i A') }}</td>
            <td>

                <!-- RESTORE -->
                <form method="POST"
                      action="{{ route('admin.inventory-items.restore', $item->id) }}"  style="display:inline">
                    @csrf
                    <button class="btn btn-sm text-success">
                        Restore
                    </button>
                </form>

                <!-- PERMANENT DELETE -->
                <form method="POST"
                      action="{{ route('admin.inventory-items.force', $item->id) }}"
                      onsubmit="return confirm('This will permanently delete the user. Continue?')"  style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm text-danger">
                        Delete Permanently
                    </button>
                </form>

            </td>
        </tr>
    @empty
        <tr><td colspan="5">No clients</td></tr>
        @endforelse
    </tbody>
</table>

@endsection
