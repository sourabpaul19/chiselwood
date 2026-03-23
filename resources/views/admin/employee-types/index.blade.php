@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Employee Type Management</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Employee Type</li>
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
            action="{{ isset($employeeType)
                ? route('admin.employee-types.update', $employeeType->id)
                : route('admin.employee-types.store') }}">

            @csrf
            @isset($employeeType)
                @method('PUT')
            @endisset

            <h2>{{ isset($employeeType) ? 'Edit Employee Type' : 'Add New Employee Type' }}</h2>

            <div class="form_group mb-3">
                <label>Name</label>
                <input type="text"
                    name="name"
                    class="textbox w-100"
                    value="{{ old('name', $employeeType->name ?? '') }}"
                    placeholder="Employee Type"
                    required>
            </div>

            <div class="form_group mb-3">
                <label>Status</label><br/>
                <select name="status" class="select">
                    <option value="active"
                        {{ old('status', $employeeType->status ?? 'active') === 'active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="inactive"
                        {{ old('status', $employeeType->status ?? '') === 'inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
            </div>

            <div class="form_group mb-3">
                <button class="btn btn-theme">
                    {{ isset($employeeType) ? 'Update Employee Type' : 'Add Employee Type' }}
                </button>
            </div>
        </form>
    </div>
    <div class="col-sm-8">
        {{-- Status Filters --}}
        <div class="table_top_header">
            <ul class="status_list">
                <li><a href="{{ route('admin.employee-types.index') }}" class="{{ !request('status') ? 'active' : '' }}">All ({{ $employeeTypes->total() }})</a></li>
                <li>|</li>
                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" class="{{ request('status') == 'active' ? 'active' : '' }}">Active</a></li>
                <li>|</li>
                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}" class="{{ request('status') == 'inactive' ? 'active' : '' }}">Inactive</a></li>
                <li>|</li>
                <li><a href="{{ route('admin.employee-types.trash') }}">Trash</a></li>
            </ul>
            
            {{-- Search Form --}}
            <form method="GET" action="{{ route('admin.employee-types.index') }}" class="search_bar ps-sm-2 d-flex gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input type="search" name="search" class="textbox" value="{{ request('search') }}" placeholder="Search office..">
                <button type="submit" class="btn">Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.employee-types.index') }}" class="btn">Clear</a>
                @endif
            </form>
        </div>

        {{-- Status Filter Dropdown --}}
        <div class="table_top_header">
            <form method="GET" action="{{ route('admin.employee-types.index') }}" class="filter_bar pe-sm-2 d-flex gap-2">
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
                    <th>Employee Type</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($employeeTypes as $type)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <p><strong>{{ $type->name }}</strong></p>
                        <div class="row_action">
                    <span><a href="{{ route('admin.employee-types.index', ['edit' => $type->id]) }}">Edit</a></span>
                    <span>|</span>
                    <span><a href="{{ route('admin.employee-types.status.toggle', $type->id) }}"
                        class="text-{{ $type->status == 'active' ? 'success' : 'danger' }}"
                        onclick="
                                event.preventDefault();
                                if(confirm('Toggle {{ $type->status }} to {{ $type->status == 'active' ? 'Inactive' : 'Active' }}?')) {
                                    document.getElementById('toggle-status-{{ $type->id }}').submit();
                                }
                        ">
                            {{ $type->status == 'active' ? 'Active' : 'Inactive' }}
                        </a><form id="toggle-status-{{ $type->id }}"
                            action="{{ route('admin.employee-types.status.toggle', $type->id) }}"
                            method="POST"
                            style="display:none;">
                            @csrf
                            @method('PATCH')
                        </form></span>
                                            <span>|</span>
                                            <span>
                                                <a href="#" class="text-danger"
                                                onclick="event.preventDefault(); confirmDelete({{ $type->id }});">
                                                    Delete
                                                </a>

                                                <form id="delete-form-{{ $type->id }}"
                                                    action="{{ route('admin.employee-types.destroy', $type->id) }}"
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
                        <span class="text-{{ $type->status=='active' ? 'success':'danger' }}">
                            {{ ucfirst($type->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center">No employee type found</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $employeeTypes->links() }}
    </div>
</div>


@endsection
