@extends('layouts.admin')

@section('content')
<h2>Edit Document</h2>

@if(session('success'))
    <p style="color:green">{{ session('success') }}</p>
@endif

<form action="{{ route('admin.documents.update', $document->id) }}" method="POST" enctype="multipart/form-data">
    @csrf

    <label>Title:</label>
    <input type="text" name="title" value="{{ $document->title }}" required><br><br>

    <label>Project:</label>
    <select name="project_id" required>
        @foreach($projects as $project)
            <option value="{{ $project->id }}" {{ $project->id == $document->project_id ? 'selected' : '' }}>
                {{ $project->name }}
            </option>
        @endforeach
    </select><br><br>

    <label>Client:</label>
    <select name="client_id" required>
        @foreach($clients as $client)
            <option value="{{ $client->id }}" {{ $client->id == $document->client_id ? 'selected' : '' }}>
                {{ $client->name }}
            </option>
        @endforeach
    </select><br><br>

    <p>Current File: 
        <a href="{{ asset('storage/'.$document->file_path) }}" target="_blank">View</a>
    </p>

    <label>Replace File (optional):</label>
    <input type="file" name="file"><br><br>

    <button type="submit">Update Document</button>
</form>

@endsection