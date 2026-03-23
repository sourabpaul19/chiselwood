@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Trashed Projects</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Projects</a></li>
            <li class="breadcrumb-item active">Trashed Projects</li>
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
            <th>Project ID</th>
            <th>Name</th>
            <th>Client</th>
            <th>Deleted At</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
    @forelse($projects as $project)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $project->project_id }}</td>
            <td>{{ $project->name }}</td>
            <td>{{ optional($project->client?->user)->name }}</td>
            <td>{{ $project->deleted_at->format('d M Y') }}</td>
            <td>

                {{-- Restore --}}
                <form method="POST"
                        action="{{ route('admin.projects.restore', $project->id) }}" style="display:inline">
                    @csrf
                    <button class="btn btn-sm text-success"
                            onclick="return confirm('Restore this project?')">
                        Restore
                    </button>
                </form>

                {{-- Force Delete --}}
                <form method="POST"
                        action="{{ route('admin.projects.force', $project->id) }}" style="display:inline">
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
