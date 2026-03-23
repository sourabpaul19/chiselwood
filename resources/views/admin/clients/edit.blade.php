@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Edit Client</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
            <li class="breadcrumb-item active">Edit Client</li>
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

<form method="POST" action="{{ route('admin.clients.update', $client) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row">
        <!-- LEFT -->
        <div class="col-sm-8">
            <div class="postbox">
                <div class="postbox_header">
                    <h3>Client Information</h3>
                </div>

                <div class="postbox_body">
                    <div class="row g-3">

                        <div class="col-sm-6">
                            <div class="form_group">
                                <label>Client Name</label>
                                <input type="text" name="name" class="textbox w-100" value="{{ old('name', $client->name) }}" required>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form_group">
                                <label>Email</label>
                                <input type="email" name="email" class="textbox w-100" value="{{ old('email', $client->email) }}" readonly>
                                <small class="text-muted">
                                    Email is used as a login ID and cannot be changed.
                                </small>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form_group">
                                <label>Company Name</label>
                                <input type="text" name="company_name" class="textbox w-100" value="{{ old('company_name', $client->client->company_name) }}">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form_group">
                                <label>Phone</label>
                                <input type="text" name="phone" class="textbox w-100" value="{{ old('phone', $client->client->phone) }}">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form_group">
                                <label>Address</label>
                                <input type="text" name="address" class="textbox w-100" value="{{ old('address', $client->client->address) }}">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form_group">
                                <label>State</label>
                                <select name="client_state" class="textbox w-100" required>
                                    <option value="">Select State</option>

                                    <!-- States -->
                                    <option value="AP" {{ $client->client->client_state=='AP' ? 'selected' : '' }}>Andhra Pradesh</option>
                                    <option value="AR" {{ $client->client->client_state=='AR' ? 'selected' : '' }}>Arunachal Pradesh</option>
                                    <option value="AS" {{ $client->client->client_state=='AS' ? 'selected' : '' }}>Assam</option>
                                    <option value="BR" {{ $client->client->client_state=='BR' ? 'selected' : '' }}>Bihar</option>
                                    <option value="CG" {{ $client->client->client_state=='CG' ? 'selected' : '' }}>Chhattisgarh</option>
                                    <option value="GA" {{ $client->client->client_state=='GA' ? 'selected' : '' }}>Goa</option>
                                    <option value="GJ" {{ $client->client->client_state=='GJ' ? 'selected' : '' }}>Gujarat</option>
                                    <option value="HR" {{ $client->client->client_state=='HR' ? 'selected' : '' }}>Haryana</option>
                                    <option value="HP" {{ $client->client->client_state=='HP' ? 'selected' : '' }}>Himachal Pradesh</option>
                                    <option value="JH" {{ $client->client->client_state=='JH' ? 'selected' : '' }}>Jharkhand</option>
                                    <option value="KA" {{ $client->client->client_state=='KA' ? 'selected' : '' }}>Karnataka</option>
                                    <option value="KL" {{ $client->client->client_state=='KL' ? 'selected' : '' }}>Kerala</option>
                                    <option value="MP" {{ $client->client->client_state=='MP' ? 'selected' : '' }}>Madhya Pradesh</option>
                                    <option value="MH" {{ $client->client->client_state=='MH' ? 'selected' : '' }}>Maharashtra</option>
                                    <option value="MN" {{ $client->client->client_state=='MN' ? 'selected' : '' }}>Manipur</option>
                                    <option value="ML" {{ $client->client->client_state=='ML' ? 'selected' : '' }}>Meghalaya</option>
                                    <option value="MZ" {{ $client->client->client_state=='MZ' ? 'selected' : '' }}>Mizoram</option>
                                    <option value="NL" {{ $client->client->client_state=='NL' ? 'selected' : '' }}>Nagaland</option>
                                    <option value="OR" {{ $client->client->client_state=='OR' ? 'selected' : '' }}>Odisha</option>
                                    <option value="PB" {{ $client->client->client_state=='PB' ? 'selected' : '' }}>Punjab</option>
                                    <option value="RJ" {{ $client->client->client_state=='RJ' ? 'selected' : '' }}>Rajasthan</option>
                                    <option value="SK" {{ $client->client->client_state=='SK' ? 'selected' : '' }}>Sikkim</option>
                                    <option value="TN" {{ $client->client->client_state=='TN' ? 'selected' : '' }}>Tamil Nadu</option>
                                    <option value="TS" {{ $client->client->client_state=='TS' ? 'selected' : '' }}>Telangana</option>
                                    <option value="TR" {{ $client->client->client_state=='TR' ? 'selected' : '' }}>Tripura</option>
                                    <option value="UP" {{ $client->client->client_state=='UP' ? 'selected' : '' }}>Uttar Pradesh</option>
                                    <option value="UK" {{ $client->client->client_state=='UK' ? 'selected' : '' }}>Uttarakhand</option>
                                    <option value="WB" {{ $client->client->client_state=='WB' ? 'selected' : '' }}>West Bengal</option>

                                    <!-- Union Territories -->
                                    <option value="AN" {{ $client->client->client_state=='AN' ? 'selected' : '' }}>Andaman and Nicobar Islands</option>
                                    <option value="CH" {{ $client->client->client_state=='CH' ? 'selected' : '' }}>Chandigarh</option>
                                    <option value="DN" {{ $client->client->client_state=='DN' ? 'selected' : '' }}>Dadra and Nagar Haveli and Daman and Diu</option>
                                    <option value="DL" {{ $client->client->client_state=='DL' ? 'selected' : '' }}>Delhi</option>
                                    <option value="JK" {{ $client->client->client_state=='JK' ? 'selected' : '' }}>Jammu and Kashmir</option>
                                    <option value="LA" {{ $client->client->client_state=='LA' ? 'selected' : '' }}>Ladakh</option>
                                    <option value="LD" {{ $client->client->client_state=='LD' ? 'selected' : '' }}>Lakshadweep</option>
                                    <option value="PY" {{ $client->client->client_state=='PY' ? 'selected' : '' }}>Puducherry</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form_group">
                            <label>Pin Code</label>
                            <input type="text" name="pincode" class="textbox w-100" placeholder="Pin Code" value="{{ old('pincode', $client->client->pincode) }}">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form_group">
                            <label>GSTIN</label>
                            <input type="text" name="gstin" class="textbox w-100" placeholder="GSTIN" value="{{ old('gstin', $client->client->gstin) }}">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form_group">
                            <label>CIN</label>
                            <input type="text" name="cin" class="textbox w-100" placeholder="CIN" value="{{ old('cin', $client->client->cin) }}">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form_group">
                                <label>Social Media / WhatsApp</label>
                                <input type="text" name="social_media" class="textbox w-100" value="{{ old('social_media', $client->client->social_media) }}">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form_group">
                                <label>Preferred Communication</label>
                                <select name="preferred_communication" class="textbox w-100">
                                    <option value="email" {{ $client->client->preferred_communication == 'email' ? 'selected' : '' }}>Email</option>
                                    <option value="phone" {{ $client->client->preferred_communication == 'phone' ? 'selected' : '' }}>Phone</option>
                                    <option value="whatsapp" {{ $client->client->preferred_communication == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form_group">
                                <label>Budget Range</label>
                                <input type="text" name="budget_range" class="textbox w-100" value="{{ old('budget_range', $client->client->budget_range) }}">
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form_group">
                                <label>Notes</label>
                                <textarea name="notes" class="textbox w-100">{{ old('notes', $client->client->notes) }}</textarea>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form_group">
                                <label>Document</label>
                                <input type="file" name="document" class="textbox w-100">
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form_group">
                                <label>Current file</label><br/>
                                @if($client->client->document)
                                    <a href="{{ asset('storage/'.$client->client->document) }}" class="btn text-info" target="_blank">
                                        View Document
                                    </a>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT -->
        <div class="col-sm-4">
            <div class="postbox">
                <div class="postbox_header">
                    <h3>Publish</h3>
                </div>

                <div class="postbox_body px-0 pb-0">
                    <div class="form_group px-6">
                        <label>Status</label>
                        <select name="status" class="select">
                            <option value="active" {{ $client->client->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $client->client->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="action_box">
                        <input type="submit" class="btn btn-theme" value="Update Client">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection
