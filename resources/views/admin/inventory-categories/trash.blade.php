@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Trashed Categories</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.inventory-categories.index') }}">Inventory Category</a></li>
            <li class="breadcrumb-item active" aria-current="page">Trashed Categories</li>
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
        @foreach($categories as $category)
        <tr>
            <td>{{ $category->name }}</td>
            <td>
                
                <form method="POST"
                    action="{{ route('admin.inventory-categories.restore', $category->id) }}"
                    class="d-inline">
                    @csrf
                    <button class="btn text-success">
                        Restore
                    </button>
                </form>

     
                <form method="POST"
                    action="{{ route('admin.inventory-categories.force', $category->id) }}"
                    class="d-inline"
                    onsubmit="return confirm('This will permanently delete the category. Continue?')">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn text-danger">
                        Delete Permanently
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
