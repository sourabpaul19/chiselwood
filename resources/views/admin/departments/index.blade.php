@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Department Management</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Department</li>
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
            action="{{ isset($department)
                ? route('admin.departments.update', $department->id)
                : route('admin.departments.store') }}">

            @csrf
            @isset($department)
                @method('PUT')
            @endisset

            <h2>{{ isset($department) ? 'Edit Department' : 'Add Department' }}</h2>

            <div class="form_group mb-3">
                <label>Name</label>
                <input type="text"
                    name="name"
                    class="textbox w-100"
                    value="{{ old('name', $department->name ?? '') }}"
                    placeholder="Department Name"
                    required>
            </div>

            <div class="form_group mb-3">
                <label>Status</label><br/>
                <select name="status" class="select">
                    <option value="active"
                        {{ old('status', $department->status ?? 'active') === 'active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="inactive"
                        {{ old('status', $department->status ?? '') === 'inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
            </div>

            <div class="form_group mb-3">
                <button class="btn btn-theme">
                    {{ isset($department) ? 'Update Department' : 'Add Department' }}
                </button>
            </div>
        </form>
    </div>
    <div class="col-sm-8">
        {{-- Status Filters --}}
        <div class="table_top_header">
            <ul class="status_list">
                <li><a href="{{ route('admin.departments.index') }}" class="{{ !request('status') ? 'active' : '' }}">All ({{ $departments->total() }})</a></li>
                <li>|</li>
                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" class="{{ request('status') == 'active' ? 'active' : '' }}">Active</a></li>
                <li>|</li>
                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}" class="{{ request('status') == 'inactive' ? 'active' : '' }}">Inactive</a></li>
                <li>|</li>
                <li><a href="{{ route('admin.departments.trash') }}">Trash</a></li>
            </ul>
            
            {{-- Search Form --}}
            <form method="GET" action="{{ route('admin.departments.index') }}" class="search_bar ps-sm-2 d-flex gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input type="search" name="search" class="textbox" value="{{ request('search') }}" placeholder="Search office..">
                <button type="submit" class="btn">Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.departments.index') }}" class="btn">Clear</a>
                @endif
            </form>
        </div>

        {{-- Status Filter Dropdown --}}
        <div class="table_top_header">
            <form method="GET" action="{{ route('admin.departments.index') }}" class="filter_bar pe-sm-2 d-flex gap-2">
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
                    <th>Department Name</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                @forelse($departments as $department)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <p><strong>{{ $department->name }}</strong></p>
                        <div class="row_action">
                    <span><a href="{{ route('admin.departments.edit', $department->id) }}">Edit</a></span>
                    <span>|</span>
                    <span><a href="{{ route('admin.departments.status.toggle', $department->id) }}"
                        class="text-{{ $department->status == 'active' ? 'success' : 'danger' }}"
                        onclick="
                                event.preventDefault();
                                if(confirm('Toggle {{ $department->status }} to {{ $department->status == 'active' ? 'Inactive' : 'Active' }}?')) {
                                    document.getElementById('toggle-status-{{ $department->id }}').submit();
                                }
                        ">
                            {{ $department->status == 'active' ? 'Active' : 'Inactive' }}
                        </a><form id="toggle-status-{{ $department->id }}"
                            action="{{ route('admin.departments.status.toggle', $department->id) }}"
                            method="POST"
                            style="display:none;">
                            @csrf
                            @method('PATCH')
                        </form></span>
                                            <span>|</span>
                                            <span>
                                                <a href="#" class="text-danger"
                                                onclick="event.preventDefault(); confirmDelete({{ $department->id }});">
                                                    Delete
                                                </a>

                                                <form id="delete-form-{{ $department->id }}"
                                                    action="{{ route('admin.departments.destroy', $department->id) }}"
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
                        <span class="text-{{ $department->status=='active' ? 'success':'danger' }}">
                            {{ ucfirst($department->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center">No department found</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $departments->links() }}
    </div>
</div>


@endsection
