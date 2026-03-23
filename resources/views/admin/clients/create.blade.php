@extends('layouts.admin')

@section('content')


   
      <div class="section_header">
        <div class="d-flex align-items-center mb-2 justify-content-between">
          <h4>Add Client</h4>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Client</li>
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


<form method="POST" action="{{ route('admin.clients.store') }}" enctype="multipart/form-data">
@csrf

<div class="row">
    <div class="col-sm-8">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Client Information</h3>
                <a href="javascript:void(0)" class="postbox_toggle">
                <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
                </a>
            </div>
            <div class="postbox_body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="form_group">
                        <label>Client Name</label>
                        <input type="text" name="name" class="textbox w-100" placeholder="Client Name" required>
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
                        <label>Company Name</label>
                        <input type="text" name="company_name" class="textbox w-100" placeholder="Company Name">
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
                        <label>Address</label>
                        <input type="text" name="address" class="textbox w-100" placeholder="Address">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form_group">
                        <label>State</label>
                        <select name="client_state" class="textbox w-100" required>
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

                    <div class="col-sm-6">
                        <div class="form_group">
                        <label>Social Media / WhatsApp</label>
                        <input type="text" name="social_media" class="textbox w-100" placeholder="Social Media">
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form_group">
                            <label>Preferred Communication</label>
                            <select name="preferred_communication" class="textbox w-100">
                                <option value="email">Email</option>
                                <option value="phone">Phone</option>
                                <option value="whatsapp">WhatsApp</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form_group">
                        <label>Budget Range</label>
                        <input type="text" class="textbox w-100" name="budget_range" placeholder="Budget Range">
                        </div>
                    </div>

                    <div class="col-sm-12">
                        <div class="form_group">
                        <label>Notes / Remarks</label>
                        <textarea name="notes" class="textbox w-100" placeholder="Notes"></textarea>
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form_group">
                            <label>Documents / Agreements Upload</label>
                            <input type="file" class="textbox w-100" name="document" accept=".pdf,.jpg,.png">
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
            <a href="#" class="text-danger">Move to Trash</a>
            <input type="submit" class="btn btn-theme" value="Save">
            </div>
        </div>
        </div>
    </div>
</div>


</form>

@endsection