@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Vendor Details</h4>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.vendors.index') }}">Vendors</a></li>
            <li class="breadcrumb-item active">View Vendor</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- LEFT -->
    <div class="col-sm-8">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Vendor Information</h3>
            </div>

            <div class="postbox_body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Vendor ID</th>
                        <td>{{ $user->vendor?->vendor_id ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Vendor Name</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>Contact Person</th>
                        <td>{{ $user->vendor?->contact_person }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td>{{ $user->vendor?->phone }}</td>
                    </tr>
                    <tr>
                        <th>Vendor Category</th>
                        <td>{{ $user->vendor?->category->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>GST Number</th>
                        <td>{{ $user->vendor?->gst_number }}</td>
                    </tr>
                    <tr>
                        <th>Payment Terms</th>
                        <td>{{ $user->vendor?->payment_terms }}</td>
                    </tr>
                    <tr>
                        <th>Rating</th>
                        <td>{{ $user->vendor->rating ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Assign Projects</th>
                        <td>
                            @forelse(optional($user->vendor)->projects ?? [] as $project)
                                      
                                            
                                            <span class="text-muted">
                                                {{ $project->name }}
                                            </span>
                                       
                                    
                   
                            @empty
                                <p class="text-muted">No project assigned.</p>
                            @endforelse
                        </td>
                    </tr>
                    <tr>
                        <th>Notes</th>
                        <td>{{ $user->vendor->notes ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Document</th>
                        <td>
                            @if($user->vendor && $user->vendor->document)
                                <a href="{{ asset('storage/'.$user->vendor->document) }}" target="_blank">
                                    View Document
                                </a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- RIGHT -->
    <div class="col-sm-4">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Status</h3>
            </div>

            <div class="postbox_body px-0 pb-0">
                <div class="p-2">
                    <strong>Status:</strong>
                    <span class="{{ $user->status === 'active' ? 'text-success' : 'text-danger' }}">
                        {{ ucfirst($user->status) }}
                    </span>
                </div>
                <div class="p-2">
                    <strong>Created:</strong>
                    {{ $user->created_at?->format('d M Y') }}
                </div>

                <div class="p-2">
                    <strong>Last Updated:</strong>
                    {{ $user->updated_at?->format('d M Y') }}

                </div>

                <div class="action_box">
                
                    <a href="#" class="text-danger"
                                                onclick="event.preventDefault(); confirmDelete({{ $user->id }});">
                                                    Move to trash
                                                </a>

                                                <form id="delete-form-{{ $user->id }}"
                                                    action="{{ route('admin.vendors.destroy', $user->id) }}"
                                                    method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                    <a href="{{ route('admin.vendors.edit', $user->id) }}" class="btn btn-theme">
                        Edit Client
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
