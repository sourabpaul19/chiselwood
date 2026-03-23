@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>All Leads</h4>
        <div class="action_area">
            <a href="{{ route('admin.leads.create') }}" class="btn ms-auto">Add New Lead</a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Leads</li>
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
        <li><a href="{{ route('admin.leads.index') }}" class="{{ !request('status') ? 'active' : '' }}">All ({{ $leads->total() }})</a></li>
        <li>|</li>
        <li><a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" class="{{ request('status') == 'active' ? 'active' : '' }}">Active</a></li>
        <li>|</li>
        <li><a href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}" class="{{ request('status') == 'inactive' ? 'active' : '' }}">Inactive</a></li>
        <li>|</li>
        <li><a href="{{ route('admin.leads.trash') }}">Trash</a></li>
    </ul>
    
    {{-- Search Form --}}
    <form method="GET" action="{{ route('admin.leads.index') }}" class="search_bar ps-sm-2 d-flex gap-2">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <input type="search" name="search" class="textbox" value="{{ request('search') }}" placeholder="Search Leads..">
        <button type="submit" class="btn">Search</button>
        @if(request('search'))
            <a href="{{ route('admin.leads.index') }}" class="btn">Clear</a>
        @endif
    </form>
</div>

{{-- Status Filter Dropdown --}}
<div class="table_top_header">
    <form method="GET" action="{{ route('admin.leads.index') }}" class="filter_bar pe-sm-2 d-flex gap-2">
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        <select name="status" class="select" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Only</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive Only</option>
        </select>

        {{-- PROJECT TYPE --}}
        <select name="lead_status_id" class="select" onchange="this.form.submit()">
            <option value="">All Lead Status</option>
            @foreach($leadStatuses as $status)
                <option value="{{ $status->id }}"
                    {{ request('lead_status_id') == $status->id ? 'selected' : '' }}>
                    {{ $status->name }}
                </option>
            @endforeach
        </select>

        {{-- PROJECT TYPE --}}
        <select name="lead_source_id" class="select" onchange="this.form.submit()">
            <option value="">All Lead Source</option>
            @foreach($leadSources as $source)
                <option value="{{ $source->id }}"
                    {{ request('lead_source_id') == $source->id ? 'selected' : '' }}>
                    {{ $source->name }}
                </option>
            @endforeach
        </select>

        {{-- PROJECT TYPE --}}
        <select name="project_type_id" class="select" onchange="this.form.submit()">
            <option value="">All Types</option>
            @foreach($types as $type)
                <option value="{{ $type->id }}"
                    {{ request('project_type_id') == $type->id ? 'selected' : '' }}>
                    {{ $type->name }}
                </option>
            @endforeach
        </select>

        {{-- ASSIGNED STAFF --}}
        <select name="staff_id" class="select" onchange="this.form.submit()">
            <option value="">All Staff</option>
            @foreach($staffs as $staff)
                <option value="{{ $staff->id }}"
                    {{ request('staff_id') == $staff->id ? 'selected' : '' }}>
                    {{ $staff->user->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn">Filter</button>
    </form>
</div>


    <table class="data_table">
        <thead>
            <tr>
                <th>Lead ID</th>
                <th>Name</th>
                <th>Contact</th>
                <th>Source</th>
                <th>Status</th>
                <th>Project Type</th>
                <th>Assigned Staff</th>
                <th>Follow Up</th>
            </tr>
        </thead>
        <tbody>
        @foreach($leads as $lead)
            <tr>
                <td>{{ $lead->lead_id }}</td>
                <td>
                    <p><strong>{{ $lead->name }}</strong></p>
                    <div class="row_action">
            <span><a href="{{ route('admin.leads.show', $lead->id) }}">View</a></span>
            <span>|</span>
            <span><a href="{{ route('admin.leads.edit', $lead->id) }}">Edit</a></span>
            <span>|</span>
            <span><a href="{{ route('admin.leads.status.toggle', $lead->id) }}"
                class="text-{{ $lead->status == 'active' ? 'success' : 'danger' }}"
                onclick="
                        event.preventDefault();
                        if(confirm('Toggle {{ $lead->status }} to {{ $lead->status == 'active' ? 'Inactive' : 'Active' }}?')) {
                            document.getElementById('toggle-status-{{ $lead->id }}').submit();
                        }
                ">
                    {{ $lead->status == 'active' ? 'Active' : 'Inactive' }}
                </a><form id="toggle-status-{{ $lead->id }}"
                    action="{{ route('admin.leads.status.toggle', $lead->id) }}"
                    method="POST"
                    style="display:none;">
                    @csrf
                </form></span>
                                    <span>|</span>
                                    <span>
                                        <a href="#" class="text-danger"
                                        onclick="event.preventDefault(); confirmDelete({{ $lead->id }});">
                                            Delete
                                        </a>

                                        <form id="delete-form-{{ $lead->id }}"
                                            action="{{ route('admin.leads.destroy', $lead->id) }}"
                                            method="POST"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </span>
                                </div>
                                <button class="toggle_table"><img src="{{ asset('images/chevron-down.svg') }}" alt="Toggle"></button>

                </td>
                <td>{{ $lead->contact_details }}</td>
                <td>{{ $lead->leadsource->name ?? '-' }}</td>
                <td>{{ $lead->leadstatus->name ?? '-' }}</td>
                <td>{{ $lead->type->name ?? '-' }}</td>
                <td>{{ $lead->staff->user->name ?? '-' }} ({{ $lead->staff->staff_id ?? '' }})</td>
                <td>{{ $lead->follow_up_date ? \Carbon\Carbon::parse($lead->follow_up_date)->format('d M Y, h:i A') : '-' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

@endsection
