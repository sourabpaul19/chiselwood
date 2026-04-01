@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Add Lead</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.leads.index') }}">Leads</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Lead</li>
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

<form method="POST" action="{{ route('admin.leads.store') }}">
@csrf


<div class="row">
    <div class="col-sm-8">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Lead Information</h3>
                <a href="javascript:void(0)" class="postbox_toggle">
                    <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
                </a>
            </div>
            <div class="postbox_body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">Name</label>
                            <input name="name" class="textbox w-100" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">Email</label>
                            <input name="email" class="textbox w-100">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">Phone</label>
                            <input name="phone" class="textbox w-100">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">Lead Source</label>
                            <select name="lead_source_id" class="textbox w-100">
                                @foreach($sources as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">Inquiry Date</label>
                            <input type="date" name="inquiry_date" class="textbox w-100">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">Budget Expectation</label>
                            <input name="budget_expectation" class="textbox w-100">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">Lead Status</label>
                            <select name="lead_status_id" class="textbox w-100">
                                @foreach($statuses as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">Assign Staff</label>
                            <select name="staff_id" class="textbox w-100">
                                <option value="">-- Select --</option>
                                @foreach($staffs as $sf)
                                <option value="{{ $sf->id }}">{{ $sf->user->name }} ({{ $sf->staff_id ?? '' }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">Follow Up Date</label>
                            <input type="datetime-local" name="follow_up_date" class="textbox w-100">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form_group">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="textbox w-100"></textarea>
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
