@extends('layouts.admin')

@section('content')
<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Signed Documents</h4>
        <div class="action_area">
            <a href="{{ route('admin.documents.create') }}" class="btn ms-auto">Upload New Document</a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.documents.index') }}">All Documents</a></li>
            <li class="breadcrumb-item active" aria-current="page">Signed Documents</li>
        </ol>
    </nav>
</div>

<table class="data_table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Project</th>
            <th>Client</th>
            <th>Signed Document</th>
        </tr>
    </thead>
    <tbody>
        @foreach($documents as $doc)
        <tr>
            <td>{{ $doc->title }}</td>
            <td>{{ $doc->project->name }}</td>
            <td>{{ $doc->client->name }}</td>
            <td><a href="{{ Storage::url($doc->signed_file_path) }}" class="btn" target="_blank">Download</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection