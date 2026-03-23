@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Settings Management</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Settings Management</li>
        </ol>
    </nav>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<form method="POST"
      action="{{ route('admin.settings.update') }}"
      enctype="multipart/form-data">
@csrf

<div class="row g-3">
    <div class="col-sm-4">
        <div class="postbox">
            <div class="postbox_header">
                <h3>General Settings</h3>
                <a href="javascript:void(0)" class="postbox_toggle">
                    <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
                </a>
            </div>
            <div class="postbox_body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form_group">
                            <input class="textbox w-100" name="site_name" value="{{ setting('site_name') }}" placeholder="Site Name">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form_group">
                            <input class="textbox w-100" name="admin_email" value="{{ setting('admin_email') }}" placeholder="Admin Email">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form_group">
                            <input class="textbox w-100" name="company_name" value="{{ setting('company_name') }}" placeholder="Company Name">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form_group">
                            <input class="textbox w-100" name="company_address" value="{{ setting('company_address') }}" placeholder="Company Address">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form_group">
                            @php
                                $companyState = setting('company_state');
                            @endphp
                            <select name="company_state" class="textbox w-100" required>
                                <option value="">Select State</option>

                                <!-- States -->
                                <option value="AP" {{ $companyState =='AP' ? 'selected' : '' }}>Andhra Pradesh</option>
                                <option value="AR" {{ $companyState =='AR' ? 'selected' : '' }}>Arunachal Pradesh</option>
                                <option value="AS" {{ $companyState =='AS' ? 'selected' : '' }}>Assam</option>
                                <option value="BR" {{ $companyState =='BR' ? 'selected' : '' }}>Bihar</option>
                                <option value="CG" {{ $companyState =='CG' ? 'selected' : '' }}>Chhattisgarh</option>
                                <option value="GA" {{ $companyState =='GA' ? 'selected' : '' }}>Goa</option>
                                <option value="GJ" {{ $companyState =='GJ' ? 'selected' : '' }}>Gujarat</option>
                                <option value="HR" {{ $companyState =='HR' ? 'selected' : '' }}>Haryana</option>
                                <option value="HP" {{ $companyState =='HP' ? 'selected' : '' }}>Himachal Pradesh</option>
                                <option value="JH" {{ $companyState =='JH' ? 'selected' : '' }}>Jharkhand</option>
                                <option value="KA" {{ $companyState =='KA' ? 'selected' : '' }}>Karnataka</option>
                                <option value="KL" {{ $companyState =='KL' ? 'selected' : '' }}>Kerala</option>
                                <option value="MP" {{ $companyState =='MP' ? 'selected' : '' }}>Madhya Pradesh</option>
                                <option value="MH" {{ $companyState =='MH' ? 'selected' : '' }}>Maharashtra</option>
                                <option value="MN" {{ $companyState =='MN' ? 'selected' : '' }}>Manipur</option>
                                <option value="ML" {{ $companyState =='ML' ? 'selected' : '' }}>Meghalaya</option>
                                <option value="MZ" {{ $companyState =='MZ' ? 'selected' : '' }}>Mizoram</option>
                                <option value="NL" {{ $companyState =='NL' ? 'selected' : '' }}>Nagaland</option>
                                <option value="OR" {{ $companyState =='OR' ? 'selected' : '' }}>Odisha</option>
                                <option value="PB" {{ $companyState =='PB' ? 'selected' : '' }}>Punjab</option>
                                <option value="RJ" {{ $companyState =='RJ' ? 'selected' : '' }}>Rajasthan</option>
                                <option value="SK" {{ $companyState =='SK' ? 'selected' : '' }}>Sikkim</option>
                                <option value="TN" {{ $companyState =='TN' ? 'selected' : '' }}>Tamil Nadu</option>
                                <option value="TS" {{ $companyState =='TS' ? 'selected' : '' }}>Telangana</option>
                                <option value="TR" {{ $companyState =='TR' ? 'selected' : '' }}>Tripura</option>
                                <option value="UP" {{ $companyState =='UP' ? 'selected' : '' }}>Uttar Pradesh</option>
                                <option value="UK" {{ $companyState =='UK' ? 'selected' : '' }}>Uttarakhand</option>
                                <option value="WB" {{ $companyState =='WB' ? 'selected' : '' }}>West Bengal</option>

                                <!-- Union Territories -->
                                <option value="AN" {{ $companyState =='AN' ? 'selected' : '' }}>Andaman and Nicobar Islands</option>
                                <option value="CH" {{ $companyState =='CH' ? 'selected' : '' }}>Chandigarh</option>
                                <option value="DN" {{ $companyState =='DN' ? 'selected' : '' }}>Dadra and Nagar Haveli and Daman and Diu</option>
                                <option value="DL" {{ $companyState =='DL' ? 'selected' : '' }}>Delhi</option>
                                <option value="JK" {{ $companyState =='JK' ? 'selected' : '' }}>Jammu and Kashmir</option>
                                <option value="LA" {{ $companyState =='LA' ? 'selected' : '' }}>Ladakh</option>
                                <option value="LD" {{ $companyState =='LD' ? 'selected' : '' }}>Lakshadweep</option>
                                <option value="PY" {{ $companyState =='PY' ? 'selected' : '' }}>Puducherry</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form_group">
                            <input class="textbox w-100" name="pincode" value="{{ setting('pincode') }}" placeholder="Pincode">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Tax Settings</h3>
                <a href="javascript:void(0)" class="postbox_toggle">
                    <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
                </a>
            </div>
            <div class="postbox_body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form_group">
                            <input type="number" step="0.01" class="textbox w-100" name="gst_rate" value="{{ setting('gst_rate', 18) }}" placeholder="GST Rate %">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form_group">
                            <input class="textbox w-100 text-uppercase" name="gstin" value="{{ setting('gstin') }}" placeholder="GSTIN">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form_group">
                            <input type="number" step="0.01" class="textbox w-100" name="cess" value="{{ setting('cess') }}" placeholder="Additional Cess %">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Email (SMTP) Settings</h3>
                <a href="javascript:void(0)" class="postbox_toggle">
                    <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
                </a>
            </div>
            <div class="postbox_body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form_group">
                            <input class="textbox w-100" name="smtp_host" value="{{ setting('smtp_host') }}" placeholder="SMTP Host">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form_group">
                            <input class="textbox w-100" name="smtp_port" value="{{ setting('smtp_port') }}" placeholder="SMTP Port">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form_group">
                            <input class="textbox w-100" name="smtp_username" value="{{ setting('smtp_username') }}" placeholder="SMTP Username">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form_group">
                            <input type="password" class="textbox w-100" name="smtp_password" value="{{ setting('smtp_password') }}" placeholder="SMTP Password">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Branding</h3>
                <a href="javascript:void(0)" class="postbox_toggle">
                    <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
                </a>
            </div>
            <div class="postbox_body">
                <div class="row g-3">
                    <div class="col-8">
                        <div class="form_group">
                            <input type="file" class="textbox w-100" name="logo">
                        </div>
                    </div>
                    <div class="col-4">
                        @if(setting('logo'))
                            <img src="{{ asset('storage/'.setting('logo')) }}" height="40">
                        @endif
                    </div>
                    <div class="col-8">
                        <div class="form_group">
                            <input type="file" class="textbox w-100" name="favicon">
                        </div>
                    </div>
                    <div class="col-4">
                        @if(setting('favicon'))
                            <img src="{{ asset('storage/'.setting('favicon')) }}" height="32">
                        @endif
                    </div>
                    <div class="col-8">
                        <div class="form_group">
                            <label>Authorised Signatory</label>
                            <input type="file" class="textbox w-100" name="signatory">
                        </div>
                    </div>
                    <div class="col-4">
                        @if(setting('signatory'))
                            <img src="{{ asset('storage/'.setting('signatory')) }}" height="32">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Invoice Settings</h3>
                <a href="javascript:void(0)" class="postbox_toggle">
                    <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
                </a>
            </div>
            <div class="postbox_body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="form_group">
                            <input class="textbox w-100" name="invoice_prefix" value="{{ setting('invoice_prefix','INV') }}" placeholder="Invoice Prefix">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form_group">
                            <textarea class="textbox w-100" name="invoice_footer" rows="3">{{ setting('invoice_footer') }}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form_group">
                            <button type="submit" class="btn btn-theme">Update Settings</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

</form>


@endsection