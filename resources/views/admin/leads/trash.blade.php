@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Trashed Leads</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.leads.index') }}">Leads</a></li>
            <li class="breadcrumb-item active">Trashed Leads</li>
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
            <th>#</th>
            <th>Lead ID</th>
            <th>Name</th>
            <th>Contact Details</th>
            <th>Deleted At</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
    @forelse($leads as $lead)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $lead->lead_id }}</td>
            <td>{{ $lead->name }}</td>
            <td>{{ $lead->contact_details }}</td>
            <td>{{ $lead->deleted_at->format('d M Y') }}</td>
            <td>

                {{-- Restore --}}
                <form method="POST"
                        action="{{ route('admin.leads.restore', $lead->id) }}" style="display:inline">
                    @csrf
                    <button class="btn btn-sm text-success"
                            onclick="return confirm('Restore this lead?')">
                        Restore
                    </button>
                </form>

                {{-- Force Delete --}}
                <form method="POST"
                        action="{{ route('admin.leads.force', $lead->id) }}" style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm text-danger"
                            onclick="return confirm('Permanently delete this lead?')">
                        Delete Permanently
                    </button>
                </form>

            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center">
                No trashed leads found
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
@endsection
