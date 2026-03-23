@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Edit Project</h4>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.projects.index') }}">Projects</a>
            </li>
            <li class="breadcrumb-item active">Edit Project</li>
        </ol>
    </nav>
</div>

{{-- Validation Errors --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<form action="{{ route('admin.projects.update', $project->id) }}"
      method="POST"
      enctype="multipart/form-data">
    @csrf
    @method('PUT')

<div class="row">

    {{-- LEFT SIDE --}}
    <div class="col-sm-8">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Project Information</h3>
            </div>

            <div class="postbox_body">
                <div class="row g-3">

                    {{-- Project Name --}}
                    <div class="col-md-6">
                        <div class="form_group">
                        <label class="form-label">Project Name</label>
                        <input type="text"
                               name="name"
                               class="textbox w-100"
                               value="{{ old('name', $project->name) }}"
                               required>
                               </div>
                    </div>

                    {{-- Client --}}
                    <div class="col-md-6">
                        <div class="form_group">
                        <label class="form-label">Client *</label>
                        <select name="client_id" class="textbox w-100" required>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}"
                                    {{ old('client_id', $project->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->user->name }}
                                </option>
                            @endforeach
                        </select>
                        </div>
                    </div>

                    {{-- Project Type --}}
                    <div class="col-md-4">
                        <div class="form_group">
                        <label class="form-label">Project Type *</label>
                        <select name="project_type_id" class="textbox w-100" required>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}"
                                    {{ old('project_type_id', $project->project_type_id) == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                        </div>
                    </div>

                    {{-- Project Status --}}
                    <div class="col-md-4">
                        <div class="form_group">
                        <label class="form-label">Project Status *</label>
                        <select name="project_status_id" class="textbox w-100" required>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}"
                                    {{ old('project_status_id', $project->project_status_id) == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                        </div>
                    </div>

                    {{-- Active Status --}}
                    <div class="col-md-4">
                        <div class="form_group">
                        <label class="form-label">Active Status</label>
                        <select name="status" class="textbox w-100">
                            <option value="active" {{ $project->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $project->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        </div>
                    </div>

                    {{-- Dates --}}
                    <div class="col-md-4">
                        <div class="form_group">
                        <label class="form-label">Start Date</label>
                        <input type="date"
                               id="start_date"
                               name="start_date"
                               class="textbox w-100"
                               value="{{ old('start_date', $project->start_date) }}">
                               </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                        <label class="form-label">End Date</label>
                        <input type="date"
                               id="estimated_end_date"
                               name="estimated_end_date"
                               class="textbox w-100"
                               value="{{ old('estimated_end_date', $project->estimated_end_date) }}">
                               </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                        <label class="form-label">Actual End Date</label>
                        <input type="date"
                               id="actual_end_date"
                               name="actual_end_date"
                               class="textbox w-100"
                               value="{{ old('actual_end_date', $project->actual_end_date) }}">
                               </div>
                    </div>

                    {{-- Budget --}}
                    <div class="col-md-6">
                        <div class="form_group">
                        <label class="form-label">Estimated Budget</label>
                        <input type="number"
                               name="estimated_budget"
                               class="textbox w-100"
                               value="{{ old('estimated_budget', $project->estimated_budget) }}">
                               </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form_group">
                        <label class="form-label">Actual Cost</label>
                        <input type="number"
                               name="actual_cost"
                               class="textbox w-100"
                               value="{{ old('actual_cost', $project->actual_cost) }}">
                               </div>
                    </div>

                    {{-- Location --}}
                    <div class="col-md-6">
                        <div class="form_group">
                        <label class="form-label">Location</label>
                        <input type="text"
                               name="location"
                               class="textbox w-100"
                               value="{{ old('location', $project->location) }}">
                               </div>
                    </div>

                    {{-- Progress --}}
                    <div class="col-md-6">
                        <div class="form_group">
                        <label class="form-label">Progress (%)</label>
                        <input type="number"
                               name="progress"
                               min="0" max="100"
                               class="textbox w-100"
                               value="{{ old('progress', $project->progress) }}">
                               </div>
                    </div>

                    {{-- Assign Staff --}}
                    <div class="col-md-6">
                        <div class="form_group">
                        <label class="form-label">Assign Staff</label>
                        <select name="staff_ids[]" class="form-select" multiple>
                            @foreach($staffs as $staff)
                                <option value="{{ $staff->id }}"
                                    {{ in_array($staff->id, $projectStaffIds) ? 'selected' : '' }}>
                                    {{ $staff->user->name }}
                                </option>
                            @endforeach
                        </select>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="col-md-6">
                        <div class="form_group">
                        <label class="form-label">Notes</label>
                        <textarea name="notes"
                                  class="form-control"
                                  rows="3">{{ old('notes', $project->notes) }}</textarea>
                                  </div>
                    </div>

                    {{-- Document --}}
                    <div class="col-md-6">
                        <div class="form_group">
                        <label class="form-label">Project Document</label>

                        

                        <input type="file" name="design_file" class="textbox w-100">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form_group">
                            <label class="form-label">Current file</label><br/>
                            @if($project->design_file)
                            <p>
                                <a href="{{ asset('storage/projects/'.$project->design_file) }}"
                                   target="_blank"
                                   class="btn text-info">
                                    View Current Document
                                </a>
                            </p>
                        @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT SIDE --}}
    <div class="col-sm-4">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Publish</h3>
            </div>

            <div class="postbox_body px-0 pb-0">
                <div class="form_group px-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="select">
                        <option value="active" {{ $project->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $project->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="action_box">
            <a href="#" class="text-danger" onclick="confirmDelete({{ $project->id }})">
    Move to trash
</a>

            <input type="submit" class="btn btn-theme" value="Save">
            </div>
            </div>
        </div>
    </div>

</div>
</form>
<form id="delete-form-{{ $project->id }}" 
      action="{{ route('admin.projects.destroy', $project->id) }}" 
      method="POST" 
      style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('estimated_end_date');
    const actualEndDate = document.getElementById('actual_end_date');

    if (startDate) {
        startDate.addEventListener('change', function () {
            endDate.min = this.value;
            actualEndDate.min = this.value;
        });
    }

    if (endDate) {
        endDate.addEventListener('change', function () {
            actualEndDate.min = this.value;
        });
    }
</script>
@endpush
