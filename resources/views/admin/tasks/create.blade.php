@extends('layouts.admin')

@section('content')



<div class="section_header">
        <div class="d-flex align-items-center mb-2 justify-content-between">
          <h4>Add Task</h4>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.tasks.index') }}">Tasks</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Task</li>
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


<form action="{{ route('admin.tasks.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

<div class="row">
    <div class="col-sm-8">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Task Information</h3>
                <a href="javascript:void(0)" class="postbox_toggle">
                    <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
                </a>
            </div>
            <div class="postbox_body">
                <div class="row g-3">
                <div class="col-md-9 ">
                <div class="form_group">
                    <label class="form-label">Task Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="textbox w-100"
                           value="{{ old('title') }}" required>
                </div>
                </div>

                {{-- Project --}}
                <div class="col-md-4 ">
                    <div class="form_group">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="textbox w-100">
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id')==$project->id?'selected':'' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                </div>

                {{-- Assigned Staff --}}
                <div class="col-md-4 ">
                    <div class="form_group">
                        <label class="form-label">Assigned Staff</label>
                        {{--<select name="assigned_to" class="textbox w-100">
                            <option value="">Select Staff</option>
                            @foreach($staffs as $staff)
                                <option value="{{ $staff->id }}" {{ old('assigned_to')==$staff->id?'selected':'' }}>
                                    {{ $staff->name }}
                                </option>
                            @endforeach
                        </select>--}}
                        <select name="assigned_to[]" class="form-control" multiple>
                            @foreach($staffs as $staff)
                                <option value="{{ $staff->id }}"
                                    {{ isset($task) && $task->assignees->contains($staff->id) ? 'selected' : '' }}>
                                    {{ $staff->name }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                </div>



                {{-- Priority --}}
                <div class="col-md-4 ">
                    <div class="form_group">
                    <label class="form-label">Priority</label>
                    <select name="priority_id" class="textbox w-100">
                        <option value="">Select Priority</option>
                        @foreach($priorities as $priority)
                            <option value="{{ $priority->id }}" {{ old('priority_id')==$priority->id?'selected':'' }}>
                                {{ $priority->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                </div>

                {{-- Status --}}
                <div class="col-md-4 ">
                    <div class="form_group">
                    <label class="form-label">Status</label>
                    <select name="status_id" class="textbox w-100">
                        <option value="">Select Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}" {{ old('status_id')==$status->id?'selected':'' }}>
                                {{ $status->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                </div>

                {{-- Start Date --}}
                <div class="col-md-4 ">
                    <div class="form_group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="textbox w-100"
                           value="{{ old('start_date') }}">
                </div>
                </div>

                {{-- Due Date --}}
                <div class="col-md-4 ">
                    <div class="form_group">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="textbox w-100"
                           value="{{ old('due_date') }}">
                </div>
                </div>

                {{-- Description --}}
                <div class="col-md-12 ">
                <div class="form_group">
                    <label class="form-label">Description / Notes</label>
                    <textarea name="description" rows="4"
                              class="textbox w-100">{{ old('description') }}</textarea>
                </div>
                </div>

                {{-- Documents --}}
                <div class="col-md-12">
                <div class="form_group">
                    <label class="form-label">Upload Documents</label>
                    <input type="file" name="documents[]" class="textbox w-100" multiple>
                    <small class="text-muted">You can upload multiple files</small>
                </div>
                </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Publish Box -->
    <div class="col-sm-4">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Publish</h3>
                <a href="javascript:void(0)" class="postbox_toggle">
                    <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
                </a>
            </div>
            <div class="postbox_body px-0 pb-0">
                <div class="form_group px-6">
                    <label class="form-label">Status</label>
                    <select id="status" name="status" class="select @error('status') is-invalid @enderror">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="action_box">
                    <!-- <a href="#" class="text-danger">Move to Trash</a> -->
                    <input type="submit" class="btn btn-theme" value="Save">
                </div>
            </div>
        </div>
    </div>
</div>

</form>

@endsection
