@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Trashed Project Types</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.project-types.index') }}">Project Type</a></li>
            <li class="breadcrumb-item active" aria-current="page">Trashed Project Types</li>
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
    @forelse($types as $type)
    <tr>
        <td>{{ $type->name }}</td>
        <td>{{ ucfirst($type->status) }}</td>
        <td class="d-flex gap-2">
            <form method="POST"
                  action="{{ route('admin.project-types.restore', $type->id) }}">
                @csrf
                <button class="btn text-success">Restore</button>
            </form>

            <form method="POST"
                  action="{{ route('admin.project-types.force', $type->id) }}"
                  onsubmit="return confirm('Permanent delete?')">
                @csrf @method('DELETE')
                <button class="btn text-danger">Delete Permanently</button>
            </form>
        </td>
    </tr>
    @empty
    <tr>
        <td colspan="3" class="text-center">Trash empty</td>
    </tr>
    @endforelse
    </tbody>
</table>
@endsection
