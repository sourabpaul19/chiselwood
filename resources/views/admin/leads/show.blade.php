@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Lead Details</h4>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.leads.index') }}">Leads</a></li>
            <li class="breadcrumb-item active">View Lead</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- LEFT -->
    <div class="col-sm-8">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Lead Information</h3>
            </div>

            <div class="postbox_body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Lead ID</th>
                        <td>{{ $lead->lead_id ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Lead Name</th>
                        <td>{{ $lead->name }}</td>
                    </tr>
                    <tr>
                        <th>Contact Details</th>
                        <td>{{ $lead->contact_details ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Lead Source</th>
                        <td>{{ $lead->leadsource->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Lead Status</th>
                        <td>{{ $lead->leadstatus->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Inquiry Date</th>
                        <td>
                            {{ $lead->inquiry_date 
                                ? \Carbon\Carbon::parse($lead->inquiry_date)->format('d M, Y') 
                                : '-' 
                            }}
                        </td>
                    </tr>
                    <tr>
                        <th>Budget Expectation</th>
                        <td>{{ $lead->budget_expectation ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Project Type</th>
                        <td>{{ $lead->type->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Assign Staff</th>
                        <td>{{ $lead->staff->user->name ?? '-' }} ({{ $lead->staff->staff_id ?? '' }})</td>
                    </tr>
                    <tr>
                        <th>Follow Up Date</th>
                        <td>{{ $lead->follow_up_date ? \Carbon\Carbon::parse($lead->follow_up_date)->format('d M Y, h:i A') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Notes</th>
                        <td>{{ $lead->notes ?? '' }}</td>
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
                    <span class="{{ $lead->status === 'active' ? 'text-success' : 'text-danger' }}">
                        {{ ucfirst($lead->status) }}
                    </span>
                </div>
                <div class="p-2">
                    <strong>Created:</strong>
                    {{ $lead->created_at->format('d M Y, h:i A') }}
                </div>

                <div class="p-2">
                    <strong>Last Updated:</strong>
                    {{ $lead->updated_at->format('d M Y, h:i A') }}
                </div>

                <div class="action_box">
                
                    <a href="#" class="text-danger"
                                                onclick="event.preventDefault(); confirmDelete({{ $lead->id }});">
                                                    Move to trash
                                                </a>

                                                <form id="delete-form-{{ $lead->id }}"
                                                    action="{{ route('admin.leads.destroy', $lead->id) }}"
                                                    method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                    <a href="{{ route('admin.leads.edit', $lead->id) }}" class="btn btn-theme">
                        Edit Client
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
