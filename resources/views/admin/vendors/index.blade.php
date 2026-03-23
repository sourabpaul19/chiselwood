@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Vendor Management</h4>
        <div class="action_area">
            <a href="{{ route('admin.vendors.create') }}" class="btn ms-auto">Add New Vendor</a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Vendors</li>
        </ol>
    </nav>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

{{-- Status Filters --}}
<div class="table_top_header">
    <ul class="status_list">
        <li><a href="{{ route('admin.vendors.index') }}" class="{{ !request('status') ? 'active' : '' }}">All ({{ $vendors->total() }})</a></li>
        <li>|</li>
        <li><a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" class="{{ request('status') == 'active' ? 'active' : '' }}">Active</a></li>
        <li>|</li>
        <li><a href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}" class="{{ request('status') == 'inactive' ? 'active' : '' }}">Inactive</a></li>
        <li>|</li>
        <li><a href="{{ route('admin.vendors.trash') }}">Trash</a></li>
    </ul>
    
    {{-- Search Form --}}
    <form method="GET" action="{{ route('admin.vendors.index') }}" class="search_bar ps-sm-2 d-flex gap-2 m-0">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <input type="search" name="search" class="textbox" value="{{ request('search') }}" placeholder="Search Vendor..">
        <button type="submit" class="btn">Search</button>
        @if(request('search'))
            <a href="{{ route('admin.vendors.index') }}" class="btn">Clear</a>
        @endif
    </form>
</div>

{{-- Status Filter Dropdown --}}
<div class="table_top_header">
    <form method="GET" action="{{ route('admin.vendors.index') }}" class="filter_bar pe-sm-2 d-flex gap-2 m-0">
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        <select name="status" class="select" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Only</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive Only</option>
        </select>

        <select name="vendor_category_id" class="select" onchange="this.form.submit()">
            <option value="">All Vendor Categories</option>
            @foreach($categories as $d)
                <option value="{{ $d->id }}" @selected(request('vendor_category_id')==$d->id)>
                    {{ $d->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn">Filter</button>
    </form>
</div>

<table class="data_table">
    <thead>
        <tr>
            <th>Vendor ID</th>
            <th>Name</th>
            <th>Category</th>
            <th>Phone</th>
            <th>Assign Projects</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vendors as $vendor)
        <tr>
            <td>{{ $vendor->vendor?->vendor_id ?? '-' }}</td>
            <td>
                <p><strong>{{ $vendor->name }}</strong></p>
                <div class="row_action">
                    <span><a href="{{ route('admin.vendors.show', $vendor->id) }}">View</a></span>
                    <span>|</span>
                    <span><a href="{{ route('admin.vendors.edit', $vendor->id) }}">Edit</a></span>
                    <span>|</span>
                    <span><a href="{{ route('admin.vendors.status.toggle', $vendor->id) }}"
                        class="text-{{ $vendor->status == 'active' ? 'success' : 'danger' }}"
                        onclick="
                                event.preventDefault();
                                if(confirm('Toggle {{ $vendor->status }} to {{ $vendor->status == 'active' ? 'Inactive' : 'Active' }}?')) {
                                    document.getElementById('toggle-status-{{ $vendor->id }}').submit();
                                }
                        ">
                            {{ $vendor->status == 'active' ? 'Active' : 'Inactive' }}
                        </a><form id="toggle-status-{{ $vendor->id }}"
                            action="{{ route('admin.vendors.status.toggle', $vendor->id) }}"
                            method="POST"
                            style="display:none;">
                            @csrf
                        </form></span>
                                            <span>|</span>
                                            <span>
                                                <a href="#" class="text-danger"
                                                onclick="event.preventDefault(); confirmDelete({{ $vendor->id }});">
                                                    Delete
                                                </a>

                                                <form id="delete-form-{{ $vendor->id }}"
                                                    action="{{ route('admin.vendors.destroy', $vendor->id) }}"
                                                    method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </span>
                                        </div>
                                        <button class="toggle_table"><img src="{{ asset('images/chevron-down.svg') }}" alt="Toggle"></button>

            </td>
            <td>{{ $vendor->vendor?->category->name ?? '-' }}</td>
            <td>{{ $vendor->vendor?->phone }}</td>
            <td>
                @if($vendor->vendor?->projects->count())
                    @foreach($vendor->vendor->projects as $project)
                        <span class="badge bg-primary">
                            {{ $project->name }}
                        </span>
                    @endforeach
                @else
                    <span class="text-muted">-</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
