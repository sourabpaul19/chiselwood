@extends('layouts.admin')
@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>All Projects</h4>
        <div class="action_area">
            <a href="{{ route('admin.projects.create') }}" class="btn ms-auto">Add New Project</a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Projects</li>
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
        <li><a href="{{ route('admin.projects.index') }}" class="{{ !request('status') ? 'active' : '' }}">All ({{ $projects->total() }})</a></li>
        <li>|</li>
        <li><a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" class="{{ request('status') == 'active' ? 'active' : '' }}">Active</a></li>
        <li>|</li>
        <li><a href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}" class="{{ request('status') == 'inactive' ? 'active' : '' }}">Inactive</a></li>
        <li>|</li>
        <li><a href="{{ route('admin.projects.trash') }}">Trash</a></li>
    </ul>
    
    {{-- Search Form --}}
    <form method="GET" action="{{ route('admin.projects.index') }}" class="search_bar ps-sm-2 d-flex gap-2">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <input type="search" name="search" class="textbox" value="{{ request('search') }}" placeholder="Search Projects..">
        <button type="submit" class="btn">Search</button>
        @if(request('search'))
            <a href="{{ route('admin.projects.index') }}" class="btn">Clear</a>
        @endif
    </form>
</div>

{{-- Status Filter Dropdown --}}
<div class="table_top_header">
    <form method="GET" action="{{ route('admin.projects.index') }}" class="filter_bar pe-sm-2 d-flex gap-2">
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        <select name="status" class="select" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Only</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive Only</option>
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

        {{-- PROJECT STATUS --}}
        <select name="project_status_id" class="select" onchange="this.form.submit()">
            <option value="">All Project Status</option>
            @foreach($statuses as $status)
                <option value="{{ $status->id }}"
                    {{ request('project_status_id') == $status->id ? 'selected' : '' }}>
                    {{ $status->name }}
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


        <select name="client_id" class="select" onchange="this.form.submit()">
            <option value="">All Clients</option>
            @foreach($clients as $client)
                <option value="{{ $client->id }}"
                    {{ request('client_id') == $client->id ? 'selected' : '' }}>
                    {{ $client->user->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn">Filter</button>
    </form>
</div>




<table class="data_table">
    <thead class="table-light">
        <tr>
            <th>Project ID</th>
            <th>Project Name</th>
            <th>Client Name</th>
            <th>Project Type</th>
            <th>Project Status</th>
            <th>Progress</th>
            <th>Assigned Staff</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
    @forelse($projects as $project)
        <tr>
            <td><strong>{{ $project->project_id }}</strong></td>

            <td>
                <p><strong>{{ $project->name }}</strong></p>
                <div class="row_action">
            <span><a href="{{ route('admin.projects.show', $project->id) }}">View</a></span>
            <span>|</span>
            <span><a href="{{ route('admin.projects.edit', $project->id) }}">Edit</a></span>
            <span>|</span>
            <span><a href="{{ route('admin.projects.status.toggle', $project->id) }}"
                class="text-{{ $project->status == 'active' ? 'success' : 'danger' }}"
                onclick="
                        event.preventDefault();
                        if(confirm('Toggle {{ $project->status }} to {{ $project->status == 'active' ? 'Inactive' : 'Active' }}?')) {
                            document.getElementById('toggle-status-{{ $project->id }}').submit();
                        }
                ">
                    {{ $project->status == 'active' ? 'Active' : 'Inactive' }}
                </a><form id="toggle-status-{{ $project->id }}"
                    action="{{ route('admin.projects.status.toggle', $project->id) }}"
                    method="POST"
                    style="display:none;">
                    @csrf
                </form></span>
                                    <span>|</span>
                                    <span>
                                        <a href="#" class="text-danger"
                                        onclick="event.preventDefault(); confirmDelete({{ $project->id }});">
                                            Delete
                                        </a>

                                        <form id="delete-form-{{ $project->id }}"
                                            action="{{ route('admin.projects.destroy', $project->id) }}"
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
                {{ $project->client?->user?->name ?? '-' }}
            </td>

            <td>
                <span class="badge bg-info">
                    {{ $project->type?->name ?? '-' }}
                </span>
            </td>

            <td>
                <span class="badge bg-secondary">
                    {{ $project->projectStatus?->name ?? '-' }}
                </span>
            </td>

            <td style="width:140px">
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar" role="progressbar"
                            style="width: {{ $project->progress ?? 0 }}%">
                    </div>
                </div>
                <small>{{ $project->progress ?? 0 }}%</small>
            </td>

            <td>
                @forelse($project->staffs as $project)
                    <span class="badge bg-light text-dark mb-1">
                        {{ $project->user->name }}
                    </span>
                @empty
                    <span class="text-muted">Not Assigned</span>
                @endforelse
            </td>
            <td>
                <a class="btn" href="{{ route('admin.projects.comments', $project->id) }}">Comments</a>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center text-muted">
                No projects found
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

{{ $projects->links() }}


@endsection
