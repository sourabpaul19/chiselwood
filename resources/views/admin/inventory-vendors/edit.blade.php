@extends('layouts.admin')

@section('content')

@php
    $isEdit = true;
@endphp

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Edit Vendor</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.vendors.index') }}">Vendors</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                Edit Vendor
            </li>
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

<form action="{{ route('admin.vendors.update', $user->id) }}" method="POST" enctype="multipart/form-data">
@csrf
@method('PUT')

<div class="row">

    {{-- LEFT COLUMN --}}
    <div class="col-sm-8">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Vendor Information</h3>
            </div>

            <div class="postbox_body">
                <div class="row g-3">

                    {{-- Vendor Name --}}
                    <div class="col-md-4">
                        <div class="form_group">
                            <label>Vendor Name</label>
                            <input type="text"
                                   name="name"
                                   class="textbox w-100"
                                   value="{{ old('vendor_name', $user->name) }}"
                                   required>
                        </div>
                    </div>

                    {{-- Contact Person --}}
                    <div class="col-md-4">
                        <div class="form_group">
                            <label>Contact Person</label>
                            <input type="text"
                                   name="contact_person"
                                   class="textbox w-100"
                                   value="{{ old('contact_person', $user->vendor->contact_person) }}">
                        </div>
                    </div>

                    {{-- Email (Read Only) --}}
                    <div class="col-md-4">
                        <div class="form_group">
                            <label>Email (Login)</label>
                            <input type="email"
                                   class="textbox w-100"
                                   value="{{ $user->email }}"
                                   readonly>
                            <small class="text-muted">Email cannot be changed</small>
                        </div>
                    </div>

                    {{-- Phone --}}
                    <div class="col-md-4">
                        <div class="form_group">
                            <label>Phone</label>
                            <input type="text"
                                   name="phone"
                                   class="textbox w-100"
                                   value="{{ old('phone', $user->vendor->phone) }}">
                        </div>
                    </div>

                    {{-- Vendor Category --}}
                    <div class="col-md-4">
                        <div class="form_group">
                            <label>Vendor Category</label>
                            <select name="vendor_category_id" class="textbox w-100">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ old('vendor_category_id', $user->vendor_category_id) == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-6">
                            <div class="form_group">
                                <label>State</label>
                                <select name="vendor_state" class="textbox w-100" required>
                                    <option value="">Select State</option>

                                    <!-- States -->
                                    <option value="AP" {{ $user->vendor->vendor_state=='AP' ? 'selected' : '' }}>Andhra Pradesh</option>
                                    <option value="AR" {{ $user->vendor->vendor_state=='AR' ? 'selected' : '' }}>Arunachal Pradesh</option>
                                    <option value="AS" {{ $user->vendor->vendor_state=='AS' ? 'selected' : '' }}>Assam</option>
                                    <option value="BR" {{ $user->vendor->vendor_state=='BR' ? 'selected' : '' }}>Bihar</option>
                                    <option value="CG" {{ $user->vendor->vendor_state=='CG' ? 'selected' : '' }}>Chhattisgarh</option>
                                    <option value="GA" {{ $user->vendor->vendor_state=='GA' ? 'selected' : '' }}>Goa</option>
                                    <option value="GJ" {{ $user->vendor->vendor_state=='GJ' ? 'selected' : '' }}>Gujarat</option>
                                    <option value="HR" {{ $user->vendor->vendor_state=='HR' ? 'selected' : '' }}>Haryana</option>
                                    <option value="HP" {{ $user->vendor->vendor_state=='HP' ? 'selected' : '' }}>Himachal Pradesh</option>
                                    <option value="JH" {{ $user->vendor->vendor_state=='JH' ? 'selected' : '' }}>Jharkhand</option>
                                    <option value="KA" {{ $user->vendor->vendor_state=='KA' ? 'selected' : '' }}>Karnataka</option>
                                    <option value="KL" {{ $user->vendor->vendor_state=='KL' ? 'selected' : '' }}>Kerala</option>
                                    <option value="MP" {{ $user->vendor->vendor_state=='MP' ? 'selected' : '' }}>Madhya Pradesh</option>
                                    <option value="MH" {{ $user->vendor->vendor_state=='MH' ? 'selected' : '' }}>Maharashtra</option>
                                    <option value="MN" {{ $user->vendor->vendor_state=='MN' ? 'selected' : '' }}>Manipur</option>
                                    <option value="ML" {{ $user->vendor->vendor_state=='ML' ? 'selected' : '' }}>Meghalaya</option>
                                    <option value="MZ" {{ $user->vendor->vendor_state=='MZ' ? 'selected' : '' }}>Mizoram</option>
                                    <option value="NL" {{ $user->vendor->vendor_state=='NL' ? 'selected' : '' }}>Nagaland</option>
                                    <option value="OR" {{ $user->vendor->vendor_state=='OR' ? 'selected' : '' }}>Odisha</option>
                                    <option value="PB" {{ $user->vendor->vendor_state=='PB' ? 'selected' : '' }}>Punjab</option>
                                    <option value="RJ" {{ $user->vendor->vendor_state=='RJ' ? 'selected' : '' }}>Rajasthan</option>
                                    <option value="SK" {{ $user->vendor->vendor_state=='SK' ? 'selected' : '' }}>Sikkim</option>
                                    <option value="TN" {{ $user->vendor->vendor_state=='TN' ? 'selected' : '' }}>Tamil Nadu</option>
                                    <option value="TS" {{ $user->vendor->vendor_state=='TS' ? 'selected' : '' }}>Telangana</option>
                                    <option value="TR" {{ $user->vendor->vendor_state=='TR' ? 'selected' : '' }}>Tripura</option>
                                    <option value="UP" {{ $user->vendor->vendor_state=='UP' ? 'selected' : '' }}>Uttar Pradesh</option>
                                    <option value="UK" {{ $user->vendor->vendor_state=='UK' ? 'selected' : '' }}>Uttarakhand</option>
                                    <option value="WB" {{ $user->vendor->vendor_state=='WB' ? 'selected' : '' }}>West Bengal</option>

                                    <!-- Union Territories -->
                                    <option value="AN" {{ $user->vendor->vendor_state=='AN' ? 'selected' : '' }}>Andaman and Nicobar Islands</option>
                                    <option value="CH" {{ $user->vendor->vendor_state=='CH' ? 'selected' : '' }}>Chandigarh</option>
                                    <option value="DN" {{ $user->vendor->vendor_state=='DN' ? 'selected' : '' }}>Dadra and Nagar Haveli and Daman and Diu</option>
                                    <option value="DL" {{ $user->vendor->vendor_state=='DL' ? 'selected' : '' }}>Delhi</option>
                                    <option value="JK" {{ $user->vendor->vendor_state=='JK' ? 'selected' : '' }}>Jammu and Kashmir</option>
                                    <option value="LA" {{ $user->vendor->vendor_state=='LA' ? 'selected' : '' }}>Ladakh</option>
                                    <option value="LD" {{ $user->vendor->vendor_state=='LD' ? 'selected' : '' }}>Lakshadweep</option>
                                    <option value="PY" {{ $user->vendor->vendor_state=='PY' ? 'selected' : '' }}>Puducherry</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form_group">
                            <label>Pin Code</label>
                            <input type="text" name="pincode" class="textbox w-100" placeholder="Pin Code" value="{{ old('pincode', $user->vendor->pincode) }}">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form_group">
                            <label>GSTIN</label>
                            <input type="text" name="gstin" class="textbox w-100" placeholder="GSTIN" value="{{ old('gstin', $user->vendor->gstin) }}">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form_group">
                            <label>CIN</label>
                            <input type="text" name="cin" class="textbox w-100" placeholder="CIN" value="{{ old('cin', $user->vendor->cin) }}">
                            </div>
                        </div>

                    {{-- Projects --}}
<div class="col-md-12">
    <div class="form_group">
        <label>Linked Projects</label>
        <select name="projects[]" class="textbox w-100" multiple>
            @foreach($projects as $project)
                <option value="{{ $project->id }}"
                    {{ in_array($project->id, $linkedProjectIds) ? 'selected' : '' }}>
                    {{ $project->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>



                    {{-- Notes --}}
                    <div class="col-md-12">
                        <div class="form_group">
                            <label>Notes</label>
                            <textarea name="notes"
                                      class="textbox w-100"
                                      rows="3">{{ old('notes', $user->vendor->notes) }}</textarea>
                        </div>
                    </div>

                    {{-- Document --}}
                    <div class="col-md-6">
                        <div class="form_group">
                            <label>Vendor Document</label>
                            <input type="file"
                                   name="document"
                                   class="textbox w-100"
                                   accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                            <label>Current Document</label><br/>
                        @if($user->vendor->document)
                                <small>
                                    <a href="{{ asset('storage/'.$user->vendor->document) }}" class="btn text-info" target="_blank">
                                        View Existing Document
                                    </a>
                                </small>
                            @endif
                            </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT COLUMN --}}
    <div class="col-sm-4">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Publish</h3>
            </div>

            <div class="postbox_body px-0 pb-0">
                <div class="form_group px-6">
                    <label>Status</label>
                    <select name="status" class="textbox w-100">
                        <option value="active"
                            {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="inactive"
                            {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>
                            Inactive
                        </option>
                    </select>
                </div>

                <div class="action_box">
                    <input type="submit" class="btn btn-theme" value="Update Vendor">
                </div>
            </div>
        </div>
    </div>

</div>

</form>

@endsection
