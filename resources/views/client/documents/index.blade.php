@extends('layouts.client')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Your Documents</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Documents</li>
        </ol>
    </nav>
</div>

<table class="data_table">
    <thead>
        <tr>
            <th>Title</th>
            <th>Project</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($documents as $doc)
        <tr>
            <td>{{ $doc->title }}</td>
            <td>{{ $doc->project->name }}</td>
            <td>{{ $doc->is_signed ? 'Signed' : 'Pending' }}</td>
            <td><a href="{{ route('client.documents.show', $doc->id) }}" class="btn">View / Sign</a></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection