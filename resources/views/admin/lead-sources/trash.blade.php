@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Trashed Lead Source</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.lead-sources.index') }}">Lead Source</a></li>
            <li class="breadcrumb-item active" aria-current="page">Trashed Lead Source</li>
        </ol>
    </nav>
</div>

<table class="data_table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($sources as $source)
        <tr>
            <td><strong>{{ $source->name }}</strong></td>
            <td>{{ ucfirst($source->status) }}</td>
            <td class="d-flex gap-2">
                <form method="POST"
                    action="{{ route('admin.lead-sources.restore', $source->id) }}">
                    @csrf
                    <button class="btn text-success btn-sm">Restore</button>
                </form>

                <form method="POST"
                    action="{{ route('admin.lead-sources.force', $source->id) }}"
                    onsubmit="return confirm('Permanent delete?')">
                    @csrf @method('DELETE')
                    <button class="btn text-danger btn-sm">Delete Permanently</button>
                </form>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="3" class="text-center text-muted">Trash empty</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection
