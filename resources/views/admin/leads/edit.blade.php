@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Edit Lead</h4>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.leads.index') }}">Leads</a>
            </li>
            <li class="breadcrumb-item active">Edit Lead</li>
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


<form action="{{ route('admin.leads.update', $lead->id) }}"
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

                    {{-- Name --}}
<div class="col-md-4">
    <div class="form_group">
    <label class="form-label">Name</label>
    <input name="name" class="textbox w-100"
           value="{{ old('name', $lead->name) }}" required>
           </div>
</div>

{{-- Contact --}}
<div class="col-md-4">
    <div class="form_group">
    <label class="form-label">Email</label>
    <input name="email" class="textbox w-100"
           value="{{ old('email', $lead->email) }}" required>
           </div>
</div>

<div class="col-md-4">
    <div class="form_group">
    <label class="form-label">Phone</label>
    <input name="phone" class="textbox w-100"
           value="{{ old('phone', $lead->phone) }}" required>
           </div>
</div>

{{-- Lead Source --}}
<div class="col-md-4">
    <div class="form_group">
    <label class="form-label">Lead Source</label>
    <select name="lead_source_id" class="textbox w-100">
        @foreach($sources as $s)
            <option value="{{ $s->id }}"
                {{ $lead->lead_source_id == $s->id ? 'selected' : '' }}>
                {{ $s->name }}
            </option>
        @endforeach
    </select>
    </div>
</div>

{{-- Inquiry Date --}}
<div class="col-md-4">
    <div class="form_group">
        <label class="form-label">Inquiry Date</label>
        <input type="date" name="inquiry_date" class="textbox w-100"
           value="{{ $lead->inquiry_date }}">
    </div>
</div>

{{-- Budget --}}
<div class="col-md-4">
    <div class="form_group">
        <label class="form-label">Budget Expectation</label>
        <input name="budget_expectation" class="textbox w-100"
            value="{{ $lead->budget_expectation }}">
    </div>
</div>

{{-- Lead Status --}}
<div class="col-md-4">
    <div class="form_group">
        <label class="form-label">Lead Status</label>
        <select name="lead_status_id" class="textbox w-100">
            @foreach($statuses as $st)
                <option value="{{ $st->id }}"
                    {{ $lead->lead_status_id == $st->id ? 'selected' : '' }}>
                    {{ $st->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>

{{-- Assign Staff --}}
<div class="col-md-4">
    <div class="form_group">
        <label class="form-label">Assign Staff</label>
        <select name="staff_id" class="textbox w-100">
            <option value="">-- Select Staff --</option>
            @foreach($staffs as $sf)
                <option value="{{ $sf->id }}"
                    {{ $lead->staff_id == $sf->id ? 'selected' : '' }}>
                    {{ $sf->user->name }} ({{ $sf->staff_id ?? '' }})
                </option>
            @endforeach
        </select>
    </div>
</div>

{{-- Follow Up --}}
<div class="col-md-4">
    <div class="form_group">
        <label class="form-label">Follow Up Date</label>
        <input type="datetime-local" name="follow_up_date" class="textbox w-100"
           value="{{ $lead->follow_up_date ? date('Y-m-d\TH:i', strtotime($lead->follow_up_date)) : '' }}">
    </div>
</div>

{{-- Notes --}}
<div class="col-md-12">
    <div class="form_group">
        <label class="form-label">Notes</label>
        <textarea name="notes" class="textbox w-100" rows="3">{{ $lead->notes }}</textarea>
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
                        <option value="active" {{ $lead->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $lead->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="action_box">
            <a href="#" class="text-danger" onclick="confirmDelete({{ $lead->id }})">
    Move to trash
</a>

            <input type="submit" class="btn btn-theme" value="Save">
            </div>
            </div>
        </div>
    </div>

</div>
</form>
<form id="delete-form-{{ $lead->id }}" 
      action="{{ route('admin.leads.destroy', $lead->id) }}" 
      method="POST" 
      style="display:none;">
    @csrf
    @method('DELETE')
</form>
@endsection
