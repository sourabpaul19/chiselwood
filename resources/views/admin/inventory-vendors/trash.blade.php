@extends('layouts.admin')

@section('content')
<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Trashed Vendors</h4>
        <div class="action_area">
            <a href="{{ route('admin.inventory-vendors.create') }}" class="btn ms-auto">Add New Vendor</a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.inventory-vendors.index') }}">Vendors</a></li>
            <li class="breadcrumb-item active">Trashed Vendors</li>
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
            <th>Vendor ID</th>
            <th>Vendor Name</th>
            <th>Email</th>
            <th>Deleted At</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
    @forelse($users as $user)
        <tr>
            <td>{{ $user->vendor?->id ?? '-' }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->deleted_at->format('d M Y, h:i A') }}</td>
            <td>

                <!-- RESTORE -->
                <form method="POST"
                      action="{{ route('admin.inventory-vendors.restore', $user->id) }}"  style="display:inline">
                    @csrf
                    <button class="btn btn-sm text-success">
                        Restore
                    </button>
                </form>

                <!-- PERMANENT DELETE -->
                <form method="POST"
                      action="{{ route('admin.inventory-vendors.force', $user->id) }}"
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
        <tr><td colspan="5" class="text-center">No vendor</td></tr>
        @endforelse
    </tbody>
</table>

@endsection

