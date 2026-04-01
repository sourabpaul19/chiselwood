@extends('layouts.client')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Project: {{ $project->name }}</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('client.projects') }}">All Projects</a></li>
            <li class="breadcrumb-item active" aria-current="page">Project: {{ $project->name }}</li>
        </ol>
    </nav>
</div>



{{-- SUCCESS --}}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Add Comment --}}
<form method="POST" action="{{ route('client.projects.comments.store') }}"  enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="project_id" value="{{ $project->id }}">
    <textarea name="comment" class="form-control" required></textarea>
    <input type="file" name="images[]" multiple class="form-control mt-2">
    <button class="btn btn-theme mt-2">Add Comment</button>
</form>

<ul class="comment_list">
    @foreach($comments as $comment)
    <li>
        <div class="comment_header">
            <figure>
                <img src="https://ui-avatars.com/api/?name={{ $comment->user->name ?? ucfirst($comment->user_type) }}" />
            </figure>
            <figcaption>
                <h6 class="fw-bold text-primary m-0">
                    <strong>{{ $comment->user->name ?? ucfirst($comment->user_type) }}</strong>
                    <small class="text-muted">• {{ $comment->created_at->diffForHumans() }}</small>
                </h6>
                <small class="text-muted">Posted on {{ $comment->created_at->format('d M Y, H:i') }}</small>
            </figcaption>
        </div>
        <div class="comment_body">
            <p class="mb-1">{{ $comment->comment }}</p>

            <div class="image_wrapper">
                @foreach($comment->images as $img)
                <figure>
                    <img src="{{ asset('storage/'.$img->image) }}">
                    <a href="{{ route('client.projects.comments.image.delete', $img->id) }}" class="delete_btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                    </a>
                </figure>
                @endforeach
            </div>

            {{-- Edit/Delete Buttons --}}
            @if($comment->user_id == auth()->id())
            <div class="comment_action">
                <a href="#" class="text-success" data-bs-toggle="modal" data-bs-target="#editCommentModal{{ $comment->id }}">
                    Edit
                </a><span>|</span>
                <a href="{{ route('client.projects.comments.delete', $comment->id) }}" class="text-danger">
                    Delete
                </a>
            </div>
            @endif
        </div>

        

        <div class="reply_body">
            <ul class="comment_list">
            @foreach($comment->replies as $reply)
            <li>
                <div class="comment_header">
                    <figure>
                        <img src="https://ui-avatars.com/api/?name={{ $reply->user->name ?? ucfirst($reply->user_type) }}" />
                    </figure>
                    <figcaption>
                        <h6 class="fw-bold text-primary m-0">
                            <strong>{{ $reply->user->name ?? ucfirst($reply->user_type) }}</strong>
                            <small class="text-muted">• {{ $reply->created_at->diffForHumans() }}</small>
                        </h6>
                        <small class="text-muted">Posted on {{ $reply->created_at->format('d M Y, H:i') }}</small>
                    </figcaption>
                </div>
                <div class="comment_body">
                    <p class="mb-1">{{ $reply->comment }}</p>

                    <div class="image_wrapper">
                        @foreach($reply->images as $img)
                        <figure>
                            <img src="{{ asset('storage/'.$img->image) }}">
                            <a href="{{ route('client.projects.comments.image.delete', $img->id) }}" class="delete_btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                            </a>
                        </figure>
                        @endforeach
                    </div>

                    {{-- Edit/Delete Buttons --}}
                    @if($reply->user_id == auth()->id())
                    <div class="comment_action">
                        <a href="#" class="text-success" data-bs-toggle="modal" data-bs-target="#editCommentModal{{ $reply->id }}">
                            Edit
                        </a><span>|</span>
                        <a href="{{ route('client.projects.comments.delete', $reply->id) }}" class="text-danger">
                            Delete
                        </a>
                    </div>
                    @endif
                </div>
            </li>
            @endforeach
        </ul>
            {{-- Reply Form --}}
            <form method="POST" action="{{ route('client.projects.comments.store') }}" enctype="multipart/form-data" class="mt-2">
                @csrf
                <input type="hidden" name="project_id" value="{{ $project->id }}">
                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <textarea name="comment" class="form-control" placeholder="Reply..." required></textarea>
                    <input type="file" name="images[]" multiple class="form-control mt-2">
                    <button class="btn btn-theme mt-2" type="submit">Reply</button>
            </form>
        </div>
    </li>
    @endforeach
</ul>
{{-- Comments List --}}
@foreach($comments as $comment)
    

                {{-- Replies --}}
                @foreach($comment->replies as $reply)
                

                {{-- Reply Edit Modal --}}
                <div class="modal fade" id="editCommentModal{{ $reply->id }}" tabindex="-1" aria-labelledby="editCommentLabel{{ $reply->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form method="POST" action="{{ route('client.projects.comments.update', $reply->id) }}" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editCommentLabel{{ $reply->id }}">Edit Reply</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <textarea name="comment" class="form-control mb-2" required>{{ $reply->comment }}</textarea>
                                    <input type="file" name="images[]" multiple class="form-control mb-2">

                                    {{-- Existing images --}}
                                    <div class="d-flex flex-wrap">
                                        @foreach($reply->images as $img)
                                        <div class="position-relative me-2 mb-2">
                                            <img src="{{ asset('storage/'.$img->image) }}" width="80" class="rounded border">
                                            <a href="{{ route('client.projects.comments.image.delete', $img->id) }}" 
                                               class="position-absolute top-0 end-0 btn btn-danger btn-sm p-1">&times;</a>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn text-danger" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn text-success">Update Reply</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                @endforeach
     

        {{-- Comment Edit Modal --}}
        <div class="modal fade" id="editCommentModal{{ $comment->id }}" tabindex="-1" aria-labelledby="editCommentLabel{{ $comment->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('client.projects.comments.update', $comment->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="editCommentLabel{{ $comment->id }}">Edit Comment</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <textarea name="comment" class="form-control mb-2" required>{{ $comment->comment }}</textarea>
                            <input type="file" name="images[]" multiple class="form-control mb-2">

                            {{-- Existing images --}}
                            <div class="d-flex flex-wrap">
                                @foreach($comment->images as $img)
                                <div class="position-relative me-2 mb-2">
                                    <img src="{{ asset('storage/'.$img->image) }}" width="80" class="rounded border">
                                    <a href="{{ route('client.projects.comments.image.delete', $img->id) }}" 
                                       class="position-absolute top-0 end-0 btn btn-danger btn-sm p-1">&times;</a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn text-danger" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn text-success">Update Comment</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @endforeach


@endsection