@extends('layouts.client')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>{{ $document->title }} | Project: {{ $document->project->name }}</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.documents.index') }}">All Documents</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $document->title }} | Project: {{ $document->project->name }}</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-sm-8">
        <iframe src="{{ asset('storage/'.$document->file_path) }}" width="100%" height="800px"></iframe>
    </div>
    <div class="col-sm-4">
        
        <div class="postbox">
            <div class="postbox_header">
                <h3>Sign Document</h3>
                <a href="javascript:void(0)" class="postbox_toggle">
                <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
                </a>
            </div>
            @if(!$document->is_signed)
            <div class="postbox_body px-0 pb-0">
                <div class="form_group px-6">
                    <canvas id="signature-pad" style="border:1px solid #000; width:100%; height:200px;"></canvas>
                    <form id="sign-form" action="{{ route('client.documents.sign', $document->id) }}" method="POST" style="display:none;">
                        @csrf
                        <input type="hidden" name="signature_image" id="signature_image">
                    </form>
                </div>
                <div class="action_box">
                    <button id="clear" class="btn" >Clear</button>
                    <button id="save" class="btn btn-theme" >Sign Document</button>
                </div>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.0/dist/signature_pad.umd.min.js"></script>
            <script>
                const canvas = document.getElementById('signature-pad');
                const signaturePad = new SignaturePad(canvas);

                document.getElementById('clear').addEventListener('click', function() {
                    signaturePad.clear();
                });

                document.getElementById('save').addEventListener('click', function() {
                    if(signaturePad.isEmpty()){
                        alert("Please provide a signature first.");
                        return;
                    }
                    const dataURL = signaturePad.toDataURL('image/png');
                    document.getElementById('signature_image').value = dataURL;
                    document.getElementById('sign-form').submit();
                });
            </script>
            @else
            <div class="postbox_body px-0 pb-0">
                <div class="form_group px-6">
                    <p>Document already signed.</p>
                </div>
                <div class="action_box">
                    <a href="{{ asset('storage/'.$document->signed_file_path) }}" target="_blank" class="btn btn-theme" >View Signed Document</a>
                </div>
            </div>
            @endif
        </div>
       
    </div>
</div>

@endsection