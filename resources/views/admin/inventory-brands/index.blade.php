@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Inventory Brands</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Inventory Brands</li>
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
    
<div class="row">
    <div class="col-sm-4 form-wrap">
        <form method="POST"
            action="{{ isset($inventoryBrand)
                ? route('admin.inventory-brands.update', $inventoryBrand->id)
                : route('admin.inventory-brands.store') }}">

            @csrf
            @isset($inventoryBrand)
                @method('PUT')
            @endisset

            <h2>{{ isset($inventoryBrand) ? 'Edit Brand' : 'Add New Brand' }}</h2>

            <div class="form_group mb-3">
                <label>Name</label>
                <input type="text"
                    name="name"
                    class="textbox w-100"
                    value="{{ old('name', $inventoryBrand->name ?? '') }}"
                    placeholder="Brand Name"
                    required>
            </div>
            <div class="form_group mb-3">
                <label>Status</label><br/>
                <select name="status" class="select">
                    <option value="active"
                        {{ old('status', $inventoryBrand->status ?? 'active') === 'active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="inactive"
                        {{ old('status', $inventoryBrand->status ?? '') === 'inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
            </div>

            <div class="form_group mb-3">
                <button class="btn btn-theme">
                    {{ isset($inventoryBrand) ? 'Update Brand' : 'Add Brand' }}
                </button>
            </div>
        </form>
    </div>
    <div class="col-sm-8">
        {{-- Status Filters --}}
        <div class="table_top_header">
            <ul class="status_list">
                <li><a href="{{ route('admin.inventory-brands.index') }}" class="{{ !request('status') ? 'active' : '' }}">All ({{ $inventoryBrands->total() }})</a></li>
                <li>|</li>
                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" class="{{ request('status') == 'active' ? 'active' : '' }}">Active</a></li>
                <li>|</li>
                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}" class="{{ request('status') == 'inactive' ? 'active' : '' }}">Inactive</a></li>
                <li>|</li>
                <li><a href="{{ route('admin.inventory-brands.trash') }}">Trash</a></li>
            </ul>
            
            {{-- Search Form --}}
            <form method="GET" action="{{ route('admin.inventory-brands.index') }}" class="search_bar ps-sm-2 d-flex gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input type="search" name="search" class="textbox" value="{{ request('search') }}" placeholder="Search Brands..">
                <button type="submit" class="btn">Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.inventory-brands.index') }}" class="btn">Clear</a>
                @endif
            </form>
        </div>

        {{-- Status Filter Dropdown --}}
        <div class="table_top_header">
            <form method="GET" action="{{ route('admin.inventory-brands.index') }}" class="filter_bar pe-sm-2 d-flex gap-2">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <select name="status" class="select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>
                <button type="submit" class="btn">Filter</button>
            </form>
        </div>
        <table class="data_table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Brand</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($inventoryBrands as $inventoryBrand)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <p><strong>{{ $inventoryBrand->name }}</strong></p>
                        <div class="row_action">
                    <span><a href="{{ route('admin.inventory-brands.index', ['edit' => $inventoryBrand->id]) }}">Edit</a></span>
                    <span>|</span>
                    <span><a href="{{ route('admin.inventory-brands.status.toggle', $inventoryBrand->id) }}"
                        class="text-{{ $inventoryBrand->status == 'active' ? 'success' : 'danger' }}"
                        onclick="
                                event.preventDefault();
                                if(confirm('Toggle {{ $inventoryBrand->status }} to {{ $inventoryBrand->status == 'active' ? 'Inactive' : 'Active' }}?')) {
                                    document.getElementById('toggle-status-{{ $inventoryBrand->id }}').submit();
                                }
                        ">
                            {{ $inventoryBrand->status == 'active' ? 'Active' : 'Inactive' }}
                        </a><form id="toggle-status-{{ $inventoryBrand->id }}"
                            action="{{ route('admin.inventory-brands.status.toggle', $inventoryBrand->id) }}"
                            method="POST"
                            style="display:none;">
                            @csrf
                            @method('PATCH')
                        </form></span>
                                            <span>|</span>
                                            <span>
                                                <a href="#" class="text-danger"
                                                onclick="event.preventDefault(); confirmDelete({{ $inventoryBrand->id }});">
                                                    Delete
                                                </a>

                                                <form id="delete-form-{{ $inventoryBrand->id }}"
                                                    action="{{ route('admin.inventory-brands.destroy', $inventoryBrand->id) }}"
                                                    method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </span>
                                        </div>
                                        <button class="toggle_table"><img src="{{ asset('images/chevron-down.svg') }}" alt="Toggle"></button>

                    </td>
                    <td>
                        <span class="text-{{ $inventoryBrand->status=='active' ? 'success':'danger' }}">
                            {{ ucfirst($inventoryBrand->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">No Brand found</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $inventoryBrands->links() }}
    </div>
</div>


@endsection
