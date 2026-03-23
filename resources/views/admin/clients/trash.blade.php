@extends('layouts.admin')

@section('content')
<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Trash Clients</h4>
        <div class="action_area">
            <a href="{{ route('admin.clients.create') }}" class="btn ms-auto">Add New Client</a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.clients.index') }}">Clients</a></li>
            <li class="breadcrumb-item active">Trash Clients</li>
        </ol>
    </nav>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
<table class="data_table">
    <thead>
        <tr>
            <th>Client ID</th>
            <th>Client Name</th>
            <th>Email</th>
            <th>Deleted At</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
    @forelse($users as $client)
        <tr>
            <td>{{ $client->client_id }}</td>
            <td>{{ $client->name }}</td>
            <td>{{ $client->email }}</td>
            <td>{{ $client->deleted_at->format('d M Y, h:i A') }}</td>
            <td>

                <!-- RESTORE -->
                <form method="POST"
                      action="{{ route('admin.clients.restore', $client->id) }}"  style="display:inline">
                    @csrf
                    <button class="btn btn-sm text-success">
                        Restore
                    </button>
                </form>

                <!-- PERMANENT DELETE -->
                <form method="POST"
                      action="{{ route('admin.clients.force', $client->id) }}"
                      onsubmit="return confirm('This will permanently delete the user. Continue?')"  style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm text-danger">
                        Delete Permanently
                    </button>
                </form>

            </td>
        </tr>
    @empty
        <tr><td colspan="5">No clients</td></tr>
        @endforelse
    </tbody>
</table>

@endsection
