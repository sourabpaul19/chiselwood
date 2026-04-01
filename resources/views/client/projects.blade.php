@extends('layouts.client')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>All Projects</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Projects</li>
        </ol>
    </nav>
</div>


    <table class="data_table">
        <thead>
            <tr>
                <th>#</th>
                <th>Project Name</th>
                <th>Status</th>
                <th>Project Type</th>
                <th>Project Status</th>
                <th>Progress</th>
                <th>Assigned Staff</th>
                <th>Start Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $key => $project)
                <tr>
                    <td>{{ $key + 1 }}</td>
                    <td>{{ $project->name }}</td>
                    <td>{{ $project->status }}</td>
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
                    <td>{{ $project->created_at->format('d M Y') }}</td>
                    <td>
                        <a class="btn" href="{{ route('client.projects.comments', $project->id) }}">Comments</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No projects found</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection