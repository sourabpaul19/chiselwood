@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <h4>Client Details</h4>
    </div>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
            <li class="breadcrumb-item active">View Client</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- LEFT -->
    <div class="col-sm-8">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Client Information</h3>
            </div>

            <div class="postbox_body">
                <table class="table table-bordered">
                    <tr>
                        <th width="30%">Client ID</th>
                        <td>{{ $client->client?->client_id }}</td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td>{{ $client->name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $client->email }}</td>
                    </tr>
                    <tr>
                        <th>Company</th>
                        <td>{{ $client->client?->company_name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td>{{ $client->client?->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Address</th>
                        <td>{{ $client->client?->address ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Social Media</th>
                        <td>{{ $client->client?->social_media ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Preferred Communication</th>
                        <td>{{ ucfirst($client->client?->preferred_communication) }}</td>
                    </tr>
                    <tr>
                        <th>Projects</th>
                        <td>{{ $client->client?->projects ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Budget Range</th>
                        <td>{{ $client->client?->budget_range ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Notes</th>
                        <td>{{ $client->client?->notes ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Document</th>
                        <td>
                            @if($client->client?->document)
                                <a href="{{ asset('storage/'.$client->client?->document) }}" target="_blank">
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
                    <span class="{{ $client->status === 'active' ? 'text-success' : 'text-danger' }}">
                        {{ ucfirst($client->status) }}
                    </span>
                </div>
                <div class="p-2">
                    <strong>Created:</strong>
                    {{ $client->created_at->format('d M Y, h:i A') }}
                </div>

                <div class="p-2">
                    <strong>Last Updated:</strong>
                    {{ $client->updated_at->format('d M Y, h:i A') }}
                </div>

                <div class="action_box">
                
                    <a href="#" class="text-danger"
                                                onclick="event.preventDefault(); confirmDelete({{ $client->id }});">
                                                    Move to trash
                                                </a>

                                                <form id="delete-form-{{ $client->id }}"
                                                    action="{{ route('admin.clients.destroy', $client->id) }}"
                                                    method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                    <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-theme">
                        Edit Client
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
