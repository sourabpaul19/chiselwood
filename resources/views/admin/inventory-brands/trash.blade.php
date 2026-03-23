@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Trashed Inventory Brands</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.inventory-brands.index') }}">Inventory Brands</a></li>
            <li class="breadcrumb-item active" aria-current="page">Trashed Inventory Brands</li>
        </ol>
    </nav>
</div>


<table class="data_table">
    <thead>
        <tr>
            <th>Brand</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($inventoryBrands as $inventoryBrand)
        <tr>
            <td>{{ $inventoryBrand->name }}</td>
            <td>
                <form action="{{ route('admin.inventory-brands.restore', $inventoryBrand->id) }}"
                    method="POST" class="d-inline">
                    @csrf
                    <button class="btn text-success"
                        onclick="return confirm('Restore this brand?')">
                        Restore
                    </button>
                </form>

                
                <form action="{{ route('admin.inventory-brands.force', $inventoryBrand->id) }}"
                    method="POST"
                    onsubmit="return confirm('Force delete this brand?');" class="d-inline">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn text-danger">
                        Delete Permanently
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
