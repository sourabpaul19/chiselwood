@extends('layouts.admin')
@section('content')

<div class="section_header">
        <div class="d-flex align-items-center mb-2 justify-content-between">
          <h4>Add Staff</h4>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.staff.index') }}">Staffs</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Staff</li>
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
<form method="POST" enctype="multipart/form-data"
      action="{{ route('admin.staff.store') }}">
@csrf

<div class="row">
    <div class="col-sm-8">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Staff Information</h3>
                <a href="javascript:void(0)" class="postbox_toggle">
                <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
                </a>
            </div>
            <div class="postbox_body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="form_group">
                        <label>Staff Name</label>
                        <input type="text" name="name" class="textbox w-100" placeholder="Staff Name" required>
                        @error('name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form_group">
                        <label>Email</label>
                        <input type="email" name="email" class="textbox w-100" placeholder="Email" required>
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form_group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="textbox w-100" placeholder="Phone">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form_group">
                            <label>Select Department</label>
                            <select name="department_id" class="textbox w-100">
                                @foreach($departments as $d)
                                    <option value="{{ $d->id }}">{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form_group">
                            <label>Select Employee Type</label>
                            <select name="employee_type_id" class="textbox w-100">
                                @foreach($types as $t)
                                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form_group">
                            <label>Designation</label>
                            <input name="designation" class="textbox w-100" placeholder="Designation">
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form_group">
                            <label>Skills / Expertise</label>
                            <input name="skills" class="textbox w-100" placeholder="Skills">
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form_group">
                            <label>Salary / Hourly Rate</label>
                            <input name="salary" class="textbox w-100" placeholder="₹ / Hour | ₹ / Month">
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form_group">
                            <label>Documents / Agreements Upload</label>
                            <input type="file" class="textbox w-100" name="document" accept=".pdf,.jpg,.png">
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="form_group">
                        <label>Notes / Remarks</label>
                        <textarea name="notes" class="textbox w-100" placeholder="Notes"></textarea>
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
            <label>Status</label>
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
