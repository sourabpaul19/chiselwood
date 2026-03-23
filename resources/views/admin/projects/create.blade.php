@extends('layouts.admin')
@section('content')

<div class="section_header">
        <div class="d-flex align-items-center mb-2 justify-content-between">
          <h4>Add Project</h4>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.projects.index') }}">Projects</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Project</li>
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


<form action="{{ route('admin.projects.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

<div class="row">
    <div class="col-sm-8">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Project Information</h3>
                <a href="javascript:void(0)" class="postbox_toggle">
                    <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
                </a>
            </div>
            <div class="postbox_body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="form_group">
                            <label class="form-label">Project Name</label>
                            <input type="text" name="name" class="textbox w-100" placeholder="Project Name" required>
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form_group">
                            <label class="form-label">Client *</label>
                            <select name="client_id" class="textbox w-100" required>
                                <option value="">Select Client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">
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
                                <option value="">Select Type</option>
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Project Status --}}
                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">Project Status *</label>
                            <select name="project_status_id" class="textbox w-100" required>
                                <option value="">Select Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}">{{ $status->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">Active Status</label>
                            <select name="status" class="textbox w-100">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    {{-- Dates --}}
                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" id="start_date" class="textbox w-100" value="{{ old('start_date') }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">End Date</label>
                            <input type="date" name="estimated_end_date" id="estimated_end_date" class="textbox w-100" value="{{ old('estimated_end_date') }}">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">Actual End Date</label>
                            <input type="date" name="actual_end_date" id="actual_end_date" class="textbox w-100" value="{{ old('actual_end_date') }}">
                        </div>
                    </div>

                    {{-- Budget --}}
                    <div class="col-md-6">
                        <div class="form_group">
                            <label class="form-label">Estimated Budget</label>
                            <input type="number" name="estimated_budget" class="textbox w-100">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form_group">
                            <label class="form-label">Actual Cost</label>
                            <input type="number" name="actual_cost" class="textbox w-100">
                        </div>
                    </div>

                    {{-- Location --}}
                    <div class="col-md-6">
                        <div class="form_group">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="textbox w-100">
                        </div>
                    </div>

                    {{-- Progress --}}
                    <div class="col-md-6">
                        <div class="form_group">
                            <label class="form-label">Progress (%)</label>
                            <input type="number" name="progress" class="textbox w-100" min="0" max="100">
                        </div>
                    </div>

                    {{-- Assign Staff --}}
                    <div class="col-md-6">
                        <div class="form_group">
                            <label class="form-label">Assign Staff</label>
                            <select name="staff_ids[]" class="form-select" multiple>
                                @foreach($staffs as $staff)
                                    <option value="{{ $staff->id }}">
                                        {{ $staff->user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl / Cmd to select multiple</small>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="col-md-6">
                        <div class="form_group">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>

                    {{-- Document Upload --}}
                    <div class="col-md-12">
                        <div class="form_group">
                            <label class="form-label">Project Document</label>
                            <input type="file" name="design_file" class="textbox w-100">
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
@push('scripts')
<script>
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('estimated_end_date');
    const actualEndDate = document.getElementById('actual_end_date');

    startDate.addEventListener('change', function () {
        endDate.min = this.value;
        actualEndDate.min = this.value;
    });

    endDate.addEventListener('change', function () {
        actualEndDate.min = this.value;
    });
</script>
@endpush