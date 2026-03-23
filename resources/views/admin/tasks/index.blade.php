@extends('layouts.admin')

@section('content')


<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>All Tasks</h4>
        <div class="action_area">
            <a href="{{ route('admin.tasks.kanban') }}" class="btn ">
                Kanban View
            </a>
            <a href="{{ route('admin.tasks.calendar') }}" class="btn">
                Calendar View
            </a>
            <a href="{{ route('admin.tasks.create') }}" class="btn ms-auto">Add New Task</a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tasks</li>
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
        <li><a href="{{ route('admin.tasks.index') }}" class="{{ !request('status') ? 'active' : '' }}">All ({{ $tasks->total() }})</a></li>
        <li>|</li>
        <li><a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" class="{{ request('status') == 'active' ? 'active' : '' }}">Active</a></li>
        <li>|</li>
        <li><a href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}" class="{{ request('status') == 'inactive' ? 'active' : '' }}">Inactive</a></li>
        <li>|</li>
        <li><a href="{{ route('admin.tasks.trash') }}">Trash</a></li>
    </ul>
    
    {{-- Search Form --}}
    <form method="GET" action="{{ route('admin.tasks.index') }}" class="search_bar ps-sm-2 d-flex gap-2">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <input type="search" name="search" class="textbox" value="{{ request('search') }}" placeholder="Search Tasks..">
        <button type="submit" class="btn">Search</button>
        @if(request('search'))
            <a href="{{ route('admin.tasks.index') }}" class="btn">Clear</a>
        @endif
    </form>
</div>



{{-- Status Filter Dropdown --}}
<div class="table_top_header">
    <form method="GET" action="{{ route('admin.tasks.index') }}" class="filter_bar pe-sm-2 d-flex gap-2">
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        <select name="status" class="select" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Only</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive Only</option>
        </select>

        {{-- PROJECT TYPE --}}
        <select name="project_id" class="select" onchange="this.form.submit()">
            <option value="">All Projects</option>
            @foreach($projects as $project)
                <option value="{{ $project->id }}" {{ request('project_id')==$project->id?'selected':'' }}>
                    {{ $project->name }}
                </option>
            @endforeach
        </select>

        {{-- PROJECT STATUS --}}
        <select name="assigned_to" class="select" onchange="this.form.submit()">
            <option value="">All Staff</option>
            @foreach($staffs as $staff)
                <option value="{{ $staff->id }}" {{ request('assigned_to')==$staff->id?'selected':'' }}>
                    {{ $staff->name }}
                </option>
            @endforeach
        </select>

        {{-- ASSIGNED STAFF --}}
        <select name="status_id" class="select" onchange="this.form.submit()">
            <option value="">All Status</option>
            @foreach($statuses as $status)
                <option value="{{ $status->id }}" {{ request('status_id')==$status->id?'selected':'' }}>
                    {{ $status->name }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="btn">Filter</button>
    </form>
</div>


{{-- Task Table --}}

<table class="data_table">
    <thead class="table-light">
        <tr>
            <th>Task ID</th>
            <th>Title</th>
            <th>Project</th>
            <th>Assigned To</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Start Date</th>
            <th>Due Date</th>
            <th>Actual Due Date</th>
        </tr>
    </thead>
    <tbody>
        @forelse($tasks as $task)
            <tr>
                <td><strong>{{ $task->task_id }}</strong></td>
                <td>
                    <p><strong>{{ $task->title }}</strong></p>
                    <div class="row_action">
            <span><a href="{{ route('admin.tasks.show', $task->id) }}">View</a></span>
            <span>|</span>
            <span><a href="{{ route('admin.tasks.edit', $task->id) }}">Edit</a></span>
            <span>|</span>
            <span><a href="{{ route('admin.tasks.status.toggle', $task->id) }}"
                class="text-{{ $task->status == 'active' ? 'success' : 'danger' }}"
                onclick="
                        event.preventDefault();
                        if(confirm('Toggle {{ $task->status }} to {{ $task->status == 'active' ? 'Inactive' : 'Active' }}?')) {
                            document.getElementById('toggle-status-{{ $task->id }}').submit();
                        }
                ">
                    {{ $task->status == 'active' ? 'Active' : 'Inactive' }}
                </a><form id="toggle-status-{{ $task->id }}"
                    action="{{ route('admin.tasks.status.toggle', $task->id) }}"
                    method="POST"
                    style="display:none;">
                    @csrf
                </form></span>
                                    <span>|</span>
                                    <span>
                                        <a href="#" class="text-danger"
                                        onclick="event.preventDefault(); confirmDelete({{ $task->id }});">
                                            Delete
                                        </a>

                                        <form id="delete-form-{{ $task->id }}"
                                            action="{{ route('admin.tasks.destroy', $task->id) }}"
                                            method="POST"
                                            style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </span>
                                </div>
                                <button class="toggle_table"><img src="{{ asset('images/chevron-down.svg') }}" alt="Toggle"></button>
                </td>
                <td>{{ $task->project->name ?? '-' }}</td>
                <td>@forelse($task->assignees as $user)
                        <span class="badge bg-primary me-1">
                            {{ $user->name }}
                        </span>
                    @empty
                        <span class="text-muted">Not Assigned</span>
                    @endforelse

                </td>

                {{-- Priority Badge --}}
                <td>
                    @if($task->priority)
                        <span class="badge 
                            @if($task->priority->name=='High') bg-danger
                            @elseif($task->priority->name=='Medium') bg-warning
                            @else bg-success @endif">
                            {{ $task->priority->name }}
                        </span>
                    @else
                        -
                    @endif
                </td>

                {{-- Status Badge --}}
                <td>
                    @if($task->statusInfo)
                        <span class="badge bg-info">
                            {{ $task->statusInfo->name }}
                        </span>
                    @else
                        -
                    @endif
                </td>

                <td>{{ $task->start_date?->format('d M Y') }}</td>
                <td>{{ $task->due_date?->format('d M Y') }}</td>
                <td>
    @if($task->actual_due_date)
        <span class="badge bg-success">
            {{ \Carbon\Carbon::parse($task->actual_due_date)->format('d M Y') }}
        </span>
    @else
        <span class="text-muted">—</span>
    @endif
</td>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="text-center py-4">No tasks found</td>
            </tr>
        @endforelse
    </tbody>
</table>

@endsection
