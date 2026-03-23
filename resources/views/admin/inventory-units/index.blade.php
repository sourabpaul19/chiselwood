@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Inventory Units</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Inventory Units</li>
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
            action="{{ isset($inventoryUnit)
                ? route('admin.inventory-units.update', $inventoryUnit->id)
                : route('admin.inventory-units.store') }}">

            @csrf
            @isset($inventoryUnit)
                @method('PUT')
            @endisset

            <h2>{{ isset($inventoryUnit) ? 'Edit Unit' : 'Add New Unit' }}</h2>

            <div class="form_group mb-3">
                <label>Name</label>
                <input type="text"
                    name="name"
                    class="textbox w-100"
                    value="{{ old('name', $inventoryUnit->name ?? '') }}"
                    placeholder="Unit Name"
                    required>
            </div>

            <div class="form_group mb-3">
                <label>Short Name</label>
                <input type="text"
                    name="short_name"
                    class="textbox w-100"
                    value="{{ old('short_name', $inventoryUnit->short_name ?? '') }}"
                    placeholder="Unit Short Name"
                    required>
            </div>

            <div class="form_group mb-3">
                <label>Status</label><br/>
                <select name="status" class="select">
                    <option value="active"
                        {{ old('status', $inventoryUnit->status ?? 'active') === 'active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="inactive"
                        {{ old('status', $inventoryUnit->status ?? '') === 'inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
            </div>

            <div class="form_group mb-3">
                <button class="btn btn-theme">
                    {{ isset($inventoryUnit) ? 'Update Unit' : 'Add Unit' }}
                </button>
            </div>
        </form>
    </div>
    <div class="col-sm-8">
        {{-- Status Filters --}}
        <div class="table_top_header">
            <ul class="status_list">
                <li><a href="{{ route('admin.inventory-units.index') }}" class="{{ !request('status') ? 'active' : '' }}">All ({{ $inventoryUnits->total() }})</a></li>
                <li>|</li>
                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" class="{{ request('status') == 'active' ? 'active' : '' }}">Active</a></li>
                <li>|</li>
                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}" class="{{ request('status') == 'inactive' ? 'active' : '' }}">Inactive</a></li>
                <li>|</li>
                <li><a href="{{ route('admin.inventory-units.trash') }}">Trash</a></li>
            </ul>
            
            {{-- Search Form --}}
            <form method="GET" action="{{ route('admin.inventory-units.index') }}" class="search_bar ps-sm-2 d-flex gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input type="search" name="search" class="textbox" value="{{ request('search') }}" placeholder="Search office..">
                <button type="submit" class="btn">Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.inventory-units.index') }}" class="btn">Clear</a>
                @endif
            </form>
        </div>

        {{-- Status Filter Dropdown --}}
        <div class="table_top_header">
            <form method="GET" action="{{ route('admin.inventory-units.index') }}" class="filter_bar pe-sm-2 d-flex gap-2">
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
                    <th>Unit</th>
                    <th>Short Name</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($inventoryUnits as $inventoryUnit)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <p><strong>{{ $inventoryUnit->name }}</strong></p>
                        <div class="row_action">
                    <span><a href="{{ route('admin.inventory-units.index', ['edit' => $inventoryUnit->id]) }}">Edit</a></span>
                    <span>|</span>
                    <span><a href="{{ route('admin.inventory-units.status.toggle', $inventoryUnit->id) }}"
                        class="text-{{ $inventoryUnit->status == 'active' ? 'success' : 'danger' }}"
                        onclick="
                                event.preventDefault();
                                if(confirm('Toggle {{ $inventoryUnit->status }} to {{ $inventoryUnit->status == 'active' ? 'Inactive' : 'Active' }}?')) {
                                    document.getElementById('toggle-status-{{ $inventoryUnit->id }}').submit();
                                }
                        ">
                            {{ $inventoryUnit->status == 'active' ? 'Active' : 'Inactive' }}
                        </a><form id="toggle-status-{{ $inventoryUnit->id }}"
                            action="{{ route('admin.inventory-units.status.toggle', $inventoryUnit->id) }}"
                            method="POST"
                            style="display:none;">
                            @csrf
                            @method('PATCH')
                        </form></span>
                                            <span>|</span>
                                            <span>
                                                <a href="#" class="text-danger"
                                                onclick="event.preventDefault(); confirmDelete({{ $inventoryUnit->id }});">
                                                    Delete
                                                </a>

                                                <form id="delete-form-{{ $inventoryUnit->id }}"
                                                    action="{{ route('admin.inventory-units.destroy', $inventoryUnit->id) }}"
                                                    method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </span>
                                        </div>
                                        <button class="toggle_table"><img src="{{ asset('images/chevron-down.svg') }}" alt="Toggle"></button>

                    </td>
                    <td>{{ $inventoryUnit->short_name }}</td>
                    <td>
                        <span class="text-{{ $inventoryUnit->status=='active' ? 'success':'danger' }}">
                            {{ ucfirst($inventoryUnit->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center">No Unit found</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $inventoryUnits->links() }}
    </div>
</div>


@endsection
