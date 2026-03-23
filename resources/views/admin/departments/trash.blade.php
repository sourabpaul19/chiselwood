@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Department Trash</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.departments.index') }}">Department</a></li>
            <li class="breadcrumb-item active" aria-current="page">Department Trash</li>
        </ol>
    </nav>
</div>

<table class="data_table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($departments as $department)
        <tr>
            <td>{{ $department->name }}</td>
            <td>
                <a href="{{ route('admin.departments.restore',$department->id) }}"
                class="btn text-success">
                Restore
                </a>

                <a href="{{ route('admin.departments.force',$department->id) }}"
                onclick="return confirm('Permanent delete?')"
                class="btn text-danger">
                Delete Permanently
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
