@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>All Documents</h4>
        <div class="action_area">
            <a href="{{ route('admin.documents.create') }}" class="btn ms-auto">Upload New Document</a>
            <a href="{{ route('admin.documents.signed') }}" class="btn ms-auto">View Signed Documents</a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Documents</li>
        </ol>
    </nav>
</div>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

<table class="data_table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Project</th>
            <th>Client</th>
            <th>Status</th>
            <th>Original Document</th>
            <th>Signed Document</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($documents as $doc)
        <tr>
            <td>{{ $doc->id }}</td>
            <td>{{ $doc->title }}</td>
            <td>{{ $doc->project->name }}</td>
            <td>{{ $doc->client->name }}</td>
            <td>
                @if($doc->is_signed)
                    <span style="color:green;">Signed</span>
                @else
                    <span style="color:red;">Pending</span>
                @endif
            </td>
            <td>
                <a href="{{ Storage::url($doc->file_path) }}" class="btn" target="_blank">View Original Document</a>
            </td>
            <td>
                @if($doc->is_signed)
                    <a href="{{ Storage::url($doc->signed_file_path) }}" class="btn" target="_blank">View Signed Document</a>
                @else
                    N/A
                @endif
            </td>
            <td>
                @if(!$doc->is_signed)
                    <a href="{{ route('admin.documents.edit', $doc->id) }}" class="btn">Edit</a>
                @else
                    <span class="text-danger">Locked</span>
                @endif
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7">No documents uploaded yet.</td>
        </tr>
        @endforelse
    </tbody>
</table>
@endsection