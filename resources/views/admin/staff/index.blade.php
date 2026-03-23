@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Staff Management</h4>
        <div class="action_area">
            <a href="{{ route('admin.staff.create') }}" class="btn ms-auto">Add New Staff</a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Staffs</li>
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
        <li><a href="{{ route('admin.staff.index') }}" class="{{ !request('status') ? 'active' : '' }}">All ({{ $staffs->total() }})</a></li>
        <li>|</li>
        <li><a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" class="{{ request('status') == 'active' ? 'active' : '' }}">Active</a></li>
        <li>|</li>
        <li><a href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}" class="{{ request('status') == 'inactive' ? 'active' : '' }}">Inactive</a></li>
        <li>|</li>
        <li><a href="{{ route('admin.staff.trash') }}">Trash</a></li>
    </ul>
    
    {{-- Search Form --}}
    <form method="GET" action="{{ route('admin.staff.index') }}" class="search_bar ps-sm-2 d-flex gap-2 m-0">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <input type="search" name="search" class="textbox" value="{{ request('search') }}" placeholder="Search Staff..">
        <button type="submit" class="btn">Search</button>
        @if(request('search'))
            <a href="{{ route('admin.staff.index') }}" class="btn">Clear</a>
        @endif
    </form>
</div>

{{-- Status Filter Dropdown --}}
<div class="table_top_header">
    <form method="GET" action="{{ route('admin.staff.index') }}" class="filter_bar pe-sm-2 d-flex gap-2 m-0">
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        <select name="status" class="select" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Only</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive Only</option>
        </select>

        <select name="department_id" class="select" onchange="this.form.submit()">
            <option value="">All Departments</option>
            @foreach($departments as $d)
                <option value="{{ $d->id }}" @selected(request('department_id')==$d->id)>
                    {{ $d->name }}
                </option>
            @endforeach
        </select>

        <select name="employee_type_id" class="select" onchange="this.form.submit()">
            <option value="">All Employee Types</option>
            @foreach($employeeTypes as $et)
                <option value="{{ $et->id }}" @selected(request('employee_type_id')==$et->id)>
                    {{ $et->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn">Filter</button>
    </form>
</div>

<table class="data_table">
    <thead>
        <tr>
            <th>Staff ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>Employee Type</th>
        </tr>
    </thead>
    <tbody>
        @forelse($staffs as $staff)
        <tr>
            <td>{{ $staff->staff?->staff_id ?? '-' }}</td>
            <td>
                <p><strong>{{ $staff->name }}</strong></p>
                <div class="row_action">
                    <span><a href="{{ route('admin.staff.show', $staff->id) }}">View</a></span>
                    <span>|</span>
                    <span><a href="{{ route('admin.staff.edit', $staff->id) }}">Edit</a></span>
                    <span>|</span>
                    <span><a href="{{ route('admin.staff.status.toggle', $staff->id) }}"
                        class="text-{{ $staff->status == 'active' ? 'success' : 'danger' }}"
                        onclick="
                                event.preventDefault();
                                if(confirm('Toggle {{ $staff->status }} to {{ $staff->status == 'active' ? 'Inactive' : 'Active' }}?')) {
                                    document.getElementById('toggle-status-{{ $staff->id }}').submit();
                                }
                        ">
                            {{ $staff->status == 'active' ? 'Active' : 'Inactive' }}
                        </a><form id="toggle-status-{{ $staff->id }}"
                            action="{{ route('admin.staff.status.toggle', $staff->id) }}"
                            method="POST"
                            style="display:none;">
                            @csrf
                        </form></span>
                                            <span>|</span>
                                            <span>
                                                <a href="#" class="text-danger"
                                                onclick="event.preventDefault(); confirmDelete({{ $staff->id }});">
                                                    Delete
                                                </a>

                                                <form id="delete-form-{{ $staff->id }}"
                                                    action="{{ route('admin.staff.destroy', $staff->id) }}"
                                                    method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </span>
                                        </div>
                                        <button class="toggle_table"><img src="{{ asset('images/chevron-down.svg') }}" alt="Toggle"></button>
            </td>
            <td>{{ $staff->email }}</td>
            <td>{{ $staff->staff?->department->name ?? '-' }}</td>
            <td>{{ $staff->staff?->employeetype->name ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center">No staff found</td></tr>
        @endforelse
    </tbody>
</table>

{{ $staffs->links() }}

@endsection
