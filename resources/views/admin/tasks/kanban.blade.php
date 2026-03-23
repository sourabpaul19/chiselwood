@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Task Kanban View</h4>
        <div class="action_area">
            <a href="{{ route('admin.tasks.kanban') }}" class="btn ">
                Kanban View
            </a>
            <a href="{{ route('admin.tasks.calendar') }}" class="btn">
                Calendar View
            </a>
            <a href="{{ route('admin.tasks.create') }}" class="btn ms-auto">Add New Task</a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.tasks.index') }}">Tasks</a></li>
            <li class="breadcrumb-item active" aria-current="page">Kanban Tasks</li>
        </ol>
    </nav>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="row g-sm-3 kanban-board">

    @foreach($statuses as $status)
        <div class="col-md-3">
            <div class="card">
                <div class="card-header fw-bold">
                    {{ $status->name }}
                </div>

                <div class="card-body kanban-column"
                     data-status-id="{{ $status->id }}">

                    @foreach($tasks[$status->id] ?? [] as $task)
                        <div class="kanban-card"
                             data-task-id="{{ $task->id }}">

                            <div class="fw-bold mb-1">
                                {{ $task->task_id }}
                            </div>

                            <div>{{ $task->title }}</div>

                            <small class="text-muted">
                                
                                @forelse($task->assignees as $user)
                                    <span class="badge bg-primary me-1">
                                        {{ $user->name }}
                                    </span>
                                @empty
                                    <span class="text-muted">Not Assigned</span>
                                @endforelse
                            </small>

                            {{-- Priority --}}
                            @if($task->priority)
                                <span class="badge mt-1 d-block
                                    @if($task->priority->name=='High') bg-danger
                                    @elseif($task->priority->name=='Medium') bg-warning
                                    @else bg-success @endif">
                                    {{ $task->priority->name }}
                                </span>
                            @endif
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    @endforeach

</div>

@endsection

@push('styles')
<style>
    .card {
        padding: 10px;
        background: #fff;
        box-shadow: none;
        border-radius: 3px;
    }
    .card-header {
        padding: 8px;
        background: transparent;
        border: none;
        margin-bottom: 10px;
    }
    .kanban-column {
        min-height: 400px;
        padding: 0;
    }
    .kanban-card {
        background: #f5f5f5;
        padding: 10px;
        border-radius: 3px;
        margin-bottom: 10px;
        cursor: grab;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

<script>
document.querySelectorAll('.kanban-column').forEach(column => {

    new Sortable(column, {
        group: 'tasks',
        animation: 150,

        onEnd: function (evt) {
            let taskId = evt.item.dataset.taskId;
            let statusId = evt.to.dataset.statusId;

            fetch("{{ url('admin/tasks') }}/" + taskId + "/update-status", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    status_id: statusId
                })
            });
        }
    });

});
</script>
@endpush
