@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Project Details</h4>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Projects</a></li>
            <li class="breadcrumb-item active">View Project</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- LEFT -->
    <div class="col-sm-8">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Project Information</h3>
            </div>

            <div class="postbox_body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Project ID</th>
                        <td>{{ $project->project_id ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Project Name</th>
                        <td>{{ $project->name }}</td>
                    </tr>
                    <tr>
                        <th>Client</th>
                        <td>{{ $project->client->user->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Project Type</th>
                        <td>{{ $project->type->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Project Status</th>
                        <td>{{ $project->projectStatus->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Start Date</th>
                        <td>
                            {{ $project->start_date 
                                ? \Carbon\Carbon::parse($project->start_date)->format('d M, Y') 
                                : '-' 
                            }}
                        </td>
                    </tr>
                    <tr>
                        <th>End Date</th>
                        <td>
                            {{ $project->estimated_end_date 
                                ? \Carbon\Carbon::parse($project->estimated_end_date)->format('d M, Y') 
                                : '-' 
                            }}
                        </td>
                    </tr>
                    <tr>
                        <th>Actual End Date</th>
                        <td>
                            {{ $project->actual_end_date 
                                ? \Carbon\Carbon::parse($project->actual_end_date)->format('d M, Y') 
                                : '-' 
                            }}
                        </td>
                    </tr>
                    <tr>
                        <th>Estimated Budget</th>
                        <td>{{ $project->estimated_budget ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Actual Cost</th>
                        <td>{{ $project->actual_cost ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Location</th>
                        <td>{{ $project->location ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Progress (%)</th>
                        <td>
                            <div class="progress">
                                <div class="progress-bar"
                                    style="width: {{ $project->progress }}%">
                                    {{ $project->progress }}%
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Assign Staff</th>
                        <td>
                            @if($project->staffs->count())
                                    @foreach($project->staffs as $staff)
                                        <span>
                                            {{ $staff->user->name ?? '-' }}
                                            <span class="text-muted">
                                                ({{ $staff->staff_id ?? '' }})
                                            </span>
                                        </span>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted">No staff assigned.</p>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Notes</th>
                        <td>{{ $project->notes ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Document</th>
                        <td>
                            @if($project->design_file)
                                <a href="{{ asset('storage/'.$project->design_file) }}" target="_blank">
                                    View Document
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="col-sm-4">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Status</h3>
            </div>

            <div class="postbox_body px-0 pb-0">
                <div class="p-2">
                    <strong>Status:</strong>
                    <span class="{{ $project->status === 'active' ? 'text-success' : 'text-danger' }}">
                        {{ ucfirst($project->status) }}
                    </span>
                </div>
                <div class="p-2">
                    <strong>Created:</strong>
                    {{ $project->created_at->format('d M Y, h:i A') }}
                </div>

                <div class="p-2">
                    <strong>Last Updated:</strong>
                    {{ $project->updated_at->format('d M Y, h:i A') }}
                </div>

                <div class="action_box">
                
                    <a href="#" class="text-danger"
                                                onclick="event.preventDefault(); confirmDelete({{ $project->id }});">
                                                    Move to trash
                                                </a>

                                                <form id="delete-form-{{ $project->id }}"
                                                    action="{{ route('admin.projects.destroy', $project->id) }}"
                                                    method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                    <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn btn-theme">
                        Edit Client
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
