@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Employee Type Trash</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.employee-types.index') }}">Employee Type</a></li>
            <li class="breadcrumb-item active" aria-current="page">Employee Type Trash</li>
        </ol>
    </nav>
</div>


<table class="data_table">
    <thead>
        <tr>
            <th>Employee Type</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($employeeTypes as $type)
        <tr>
            <td>{{ $type->name }}</td>
            <td>
                <a href="{{ route('admin.employee-types.restore',$type->id) }}"
                class="btn text-success">
                Restore
                </a>

                <a href="{{ route('admin.employee-types.force',$type->id) }}"
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
