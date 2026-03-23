@extends('layouts.admin')

@section('content')
<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>All Clients</h4>
        <div class="action_area">
            <a href="{{ route('admin.clients.create') }}" class="btn ms-auto">Add New Client</a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Clients</li>
        </ol>
    </nav>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif


{{-- Status Filters --}}
<div class="table_top_header">
    <ul class="status_list">
        <li><a href="{{ route('admin.clients.index') }}" class="{{ !request('status') ? 'active' : '' }}">All ({{ $clients->total() }})</a></li>
        <li>|</li>
        <li><a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" class="{{ request('status') == 'active' ? 'active' : '' }}">Active</a></li>
        <li>|</li>
        <li><a href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}" class="{{ request('status') == 'inactive' ? 'active' : '' }}">Inactive</a></li>
        <li>|</li>
        <li><a href="{{ route('admin.clients.trash') }}">Trash</a></li>
    </ul>
    
    {{-- Search Form --}}
    <form method="GET" action="{{ route('admin.clients.index') }}" class="search_bar ps-sm-2 d-flex gap-2">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <input type="search" name="search" class="textbox" value="{{ request('search') }}" placeholder="Search office..">
        <button type="submit" class="btn">Search</button>
        @if(request('search'))
            <a href="{{ route('admin.clients.index') }}" class="btn">Clear</a>
        @endif
    </form>
</div>

{{-- Status Filter Dropdown --}}
<div class="table_top_header">
    <form method="GET" action="{{ route('admin.clients.index') }}" class="filter_bar pe-sm-2 d-flex gap-2">
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        <select name="status" class="select" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Only</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive Only</option>
        </select>
        <button type="submit" class="btn">Filter</button>
    </form>
</div>

<table class="data_table">
    <thead>
        <tr>
            <th class="column_primary">Client ID</th>
            <th class="column_primary">Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Company</th>
            <th>Projects</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($clients as $client)
        <tr>
            <td class="column_primary">{{ $client->client?->client_id ?? '-' }}</td>
            <td class="column_primary">
                <p><strong>{{ $client->name }}</strong></p>
                <div class="row_action">
                    <span><a href="{{ route('admin.clients.show', $client->id) }}">View</a></span>
                    <span>|</span>
                    <span><a href="{{ route('admin.clients.edit', $client->id) }}">Edit</a></span>
                    <span>|</span>
                    <span><a href="{{ route('admin.clients.status.toggle', $client->id) }}"
                        class="text-{{ $client->status == 'active' ? 'success' : 'danger' }}"
                        onclick="
                                event.preventDefault();
                                if(confirm('Toggle {{ $client->status }} to {{ $client->status == 'active' ? 'Inactive' : 'Active' }}?')) {
                                    document.getElementById('toggle-status-{{ $client->id }}').submit();
                                }
                        ">
                            {{ $client->status == 'active' ? 'Active' : 'Inactive' }}
                        </a><form id="toggle-status-{{ $client->id }}"
                            action="{{ route('admin.clients.status.toggle', $client->id) }}"
                            method="POST"
                            style="display:none;">
                            @csrf
                            @method('PATCH')
                        </form></span>
                                            <span>|</span>
                                            <span>
                                                <a href="#" class="text-danger"
                                                onclick="event.preventDefault(); confirmDelete({{ $client->id }});">
                                                    Delete
                                                </a>

                                                <form id="delete-form-{{ $client->id }}"
                                                    action="{{ route('admin.clients.destroy', $client->id) }}"
                                                    method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </span>
                                        </div>
                                        <button class="toggle_table"><img src="{{ asset('images/chevron-down.svg') }}" alt="Toggle"></button>


            </td>
            <td class="data-col">{{ $client->email }}</td>
            <td class="data-col">{{ $client->client?->phone }}</td>
            <td class="data-col">{{ $client->client?->company_name }}</td>
            <td>
                @if($client->client && $client->client->projects->count())
                    @foreach($client->client->projects as $project)
                        <span class="badge bg-info mb-1">
                            {{ $project->name }}
                        </span>
                    @endforeach
                @else
                    <span class="text-muted">No projects</span>
                @endif
            </td>
            <td><a href="{{ route('admin.clients.ledger', $client->client?->id) }}"
   class="btn btn-primary">
    Ledger
</a>
</td>

        </tr>
        @empty
        <tr><td colspan="7">No clients</td></tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr>
            <td class="column_primary">Client ID</td>
            <td>Name</td>
            <td>Email</td>
            <td>Phone</td>
            <td>Company</td>
            <td>Projects</td>
            <td>Action</td>
        </tr>
    </tfoot>
</table>

@endsection