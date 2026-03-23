@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Trashed Tasks</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.tasks.index') }}">Tasks</a></li>
            <li class="breadcrumb-item active">Trashed Tasks</li>
        </ol>
    </nav>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<table class="data_table">
    <thead>
        <tr>
            <th>#</th>
            <th>Task ID</th>
            <th>Title</th>
            <th>Project</th>
            <th>Assigned To</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Deleted At</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
    @forelse($tasks as $task)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $task->task_id }}</td>
            <td>{{ $task->title }}</td>
            <td>{{ $task->project->name ?? '-' }}</td>
            <td>{{ $task->assignedStaff->name ?? '-' }}</td>
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
            <td>{{ $task->deleted_at->format('d M Y') }}</td>
            <td>

                {{-- Restore --}}
                <form method="POST"
                        action="{{ route('admin.tasks.restore', $task->id) }}" style="display:inline">
                    @csrf
                    <button class="btn btn-sm text-success"
                            onclick="return confirm('Restore this project?')">
                        Restore
                    </button>
                </form>

                {{-- Force Delete --}}
                <form method="POST"
                        action="{{ route('admin.tasks.force', $task->id) }}" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm text-danger"
                            onclick="return confirm('Permanently delete this project?')">
                        Delete Permanently
                    </button>
                </form>

            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center">
                No trashed projects found
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
@endsection
