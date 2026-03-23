@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Staff Details</h4>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.staff.index') }}">Staffs</a></li>
            <li class="breadcrumb-item active">View Staff</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- LEFT -->
    <div class="col-sm-8">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Staff Information</h3>
            </div>

            <div class="postbox_body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Staff ID</th>
                        <td>{{ $user->staff?->staff_id ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>Department</th>
                        <td>{{ $user->staff?->department->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Enployee Type</th>
                        <td>{{ $user->staff?->employeetype->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td>{{ $user->staff->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Designation</th>
                        <td>{{ $user->staff->designation ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Skills</th>
                        <td>{{ $user->staff->skills ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Salary</th>
                        <td>{{ $user->staff->salary ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Notes</th>
                        <td>{{ $user->staff->notes ?? '' }}</td>
                    </tr>
                    <tr>
                        <th>Document</th>
                        <td>
                            @if($user->staff && $user->staff->document)
                                <a href="{{ asset('storage/'.$user->staff->document) }}" target="_blank">
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
                    {{ $user->created_at->format('d M Y, h:i A') }}
                </div>

                <div class="p-2">
                    <strong>Last Updated:</strong>
                    {{ $user->updated_at->format('d M Y, h:i A') }}
                </div>

                <div class="action_box">
                
                    <a href="#" class="text-danger"
                                                onclick="event.preventDefault(); confirmDelete({{ $user->id }});">
                                                    Move to trash
                                                </a>

                                                <form id="delete-form-{{ $user->id }}"
                                                    action="{{ route('admin.staff.destroy', $user->id) }}"
                                                    method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                    <a href="{{ route('admin.staff.edit', $user->id) }}" class="btn btn-theme">
                        Edit Client
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
