@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Upload Document</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.documents.index') }}">All Documents</a></li>
            <li class="breadcrumb-item active" aria-current="page">Upload Document</li>
        </ol>
    </nav>
</div>

<form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="row">
        <div class="col-sm-8">
            <div class="postbox">
                <div class="postbox_header">
                    <h3>Document Information</h3>
                    <a href="javascript:void(0)" class="postbox_toggle">
                        <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
                    </a>
                </div>
                <div class="postbox_body">
                    <div class="row g-3">
                        <div class="col-sm-12">
                            <div class="form_group">
                                <label>Title</label>
                                <input type="text" name="title" class="textbox w-100" placeholder="Enter Title" required>
                                @error('title')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="form_group">
                                <label>Project</label>
                                <select name="project_id" class="textbox w-100" required>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form_group">
                                <label>Client</label>
                                <select name="client_id" class="textbox w-100" required>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form_group">
                                <label>Document File</label>
                                <input type="file" name="file" class="textbox w-100" required>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>


        <div class="col-sm-4">
            <div class="postbox">
                <div class="postbox_header">
                    <h3>Publish</h3>
                    <a href="javascript:void(0)" class="postbox_toggle">
                    <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
                    </a>
                </div>
                <div class="postbox_body px-0 pb-0">
                    <div class="action_box">
                        <input type="submit" class="btn btn-theme" value="Upload">
                    </div>
                </div>
            </div>
        </div>
    </div>


</form>
@endsection