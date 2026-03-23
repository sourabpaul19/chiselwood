@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
          <h4>Add Vendor</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.vendors.index') }}">Vendors</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Vendor</li>
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


<form action="{{ route('admin.vendors.store') }}" method="POST" enctype="multipart/form-data">
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
                    <div class="col-md-4">
                        <div class="form_group">
                        <label class="form-label">Vendor Name</label>
                        <input type="text" name="name" class="textbox w-100"
                            value="{{ old('name',$vendor->name ?? '') }}" required>
                            </div>
                    </div>

                    {{-- Contact Person --}}
                    <div class="col-md-4">
                        <div class="form_group">
                        <label class="form-label">Contact Person</label>
                        <input type="text"
                            name="contact_person"
                            class="textbox w-100"
                            value="{{ old('contact_person', $user->vendor->contact_person ?? '') }}">
                            </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                        <label class="form-label">Status</label>
                        <select name="status" class="textbox w-100">
                            <option value="active"
                                {{ old('status',$vendor->status ?? '')=='active'?'selected':'' }}>
                                Active
                            </option>
                            <option value="inactive"
                                {{ old('status',$vendor->status ?? '')=='inactive'?'selected':'' }}>
                                Inactive
                            </option>
                        </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                        <label>Email (Login)</label>
                        <input type="email" name="email" class="textbox w-100"
                            value="{{ old('email',$vendor->email ?? '') }}" required>
                            </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="textbox w-100"
                            value="{{ old('phone',$vendor->phone ?? '') }}">
                            </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                        <label>Vendor Category</label>
                        <select name="vendor_category_id" class="textbox w-100">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ old('vendor_category_id',$vendor->vendor_category_id ?? '')==$cat->id?'selected':'' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form_group">
                        <label>Linked Projects</label>
                        <select name="projects[]" class="textbox w-100" multiple>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}"
                                    {{ isset($vendor) && $vendor->projects->contains($project->id) ? 'selected':'' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                        <label>State</label>
                        <select name="vendor_state" class="textbox w-100" required>
                            <option value="">Select State</option>

                            <!-- States -->
                            <option value="AP">Andhra Pradesh</option>
                            <option value="AR">Arunachal Pradesh</option>
                            <option value="AS">Assam</option>
                            <option value="BR">Bihar</option>
                            <option value="CG">Chhattisgarh</option>
                            <option value="GA">Goa</option>
                            <option value="GJ">Gujarat</option>
                            <option value="HR">Haryana</option>
                            <option value="HP">Himachal Pradesh</option>
                            <option value="JH">Jharkhand</option>
                            <option value="KA">Karnataka</option>
                            <option value="KL">Kerala</option>
                            <option value="MP">Madhya Pradesh</option>
                            <option value="MH">Maharashtra</option>
                            <option value="MN">Manipur</option>
                            <option value="ML">Meghalaya</option>
                            <option value="MZ">Mizoram</option>
                            <option value="NL">Nagaland</option>
                            <option value="OR">Odisha</option>
                            <option value="PB">Punjab</option>
                            <option value="RJ">Rajasthan</option>
                            <option value="SK">Sikkim</option>
                            <option value="TN">Tamil Nadu</option>
                            <option value="TS">Telangana</option>
                            <option value="TR">Tripura</option>
                            <option value="UP">Uttar Pradesh</option>
                            <option value="UK">Uttarakhand</option>
                            <option value="WB">West Bengal</option>

                            <!-- Union Territories -->
                            <option value="AN">Andaman and Nicobar Islands</option>
                            <option value="CH">Chandigarh</option>
                            <option value="DN">Dadra and Nagar Haveli and Daman and Diu</option>
                            <option value="DL">Delhi</option>
                            <option value="JK">Jammu and Kashmir</option>
                            <option value="LA">Ladakh</option>
                            <option value="LD">Lakshadweep</option>
                            <option value="PY">Puducherry</option>
                        </select>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form_group">
                        <label>Pin Code</label>
                        <input type="text" name="pincode" class="textbox w-100" placeholder="Pin Code">
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form_group">
                        <label>GSTIN</label>
                        <input type="text" name="gstin" class="textbox w-100" placeholder="GSTIN">
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form_group">
                        <label>CIN</label>
                        <input type="text" name="cin" class="textbox w-100" placeholder="CIN">
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form_group">
                        <label>Notes</label>
                        <textarea name="notes" class="textbox w-100">{{ old('notes',$vendor->notes ?? '') }}</textarea>
                        </div>
                    </div>

                    {{-- Document Upload --}}
                    <div class="col-md-6">
                        <div class="form_group">
                        <label class="form-label">Vendor Document</label>
                        <input type="file" class="textbox w-100" name="document" accept=".pdf,.jpg,.png">
                        @if(isset($user->vendor->document))
                            <small>
                                <a href="{{ asset('storage/'.$user->vendor->document) }}" target="_blank">
                                    View Document
                                </a>
                            </small>
                        @endif
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
            <select name="status" class="textbox w-100">
            <option value="active"
                {{ old('status',$vendor->status ?? '')=='active'?'selected':'' }}>
                Active
            </option>
            <option value="inactive"
                {{ old('status',$vendor->status ?? '')=='inactive'?'selected':'' }}>
                Inactive
            </option>
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
