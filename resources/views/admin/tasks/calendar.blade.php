@extends('layouts.admin')

@section('content')
<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Task Calendar View</h4>
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
            <li class="breadcrumb-item active" aria-current="page">Task Calendar</li>
        </ol>
    </nav>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif


<div class="card">
    <div class="card-body">
        <div id="taskCalendar"></div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const calendarEl = document.getElementById('taskCalendar');

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 650,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },

        events: [
            @foreach($tasks as $task)
            {
                title: "{{ $task->title }}",
                start: "{{ $task->start_date }}",
                end: "{{ $task->due_date }}",
                url: "{{ route('admin.tasks.show', $task->id) }}",
                color: "{{ $task->status_id == 3 ? '#28a745' : '#0d6efd' }}"
            },
            @endforeach
        ]
    });

    calendar.render();
});
</script>
@endpush
