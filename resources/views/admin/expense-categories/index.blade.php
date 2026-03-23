@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Expense Category Management</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Expense Category Management</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-sm-4 form-wrap">
        
        <form method="POST"
              action="{{ isset($category)
                ? route('admin.expense-categories.update', $category)
                : route('admin.expense-categories.store') }}">
            @csrf
            @isset($category) @method('PUT') @endisset

            <h2>{{ isset($category) ? 'Edit' : 'Add' }} Expense Category</h2>

            <div class="form_group mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="textbox w-100" placeholder="Expense Category" value="{{ old('name', $category->name ?? '') }}">
            </div>
            <div class="form_group mb-3">
                <label class="form-label">Status</label><br/>
                <select name="status" class="select">
                    <option value="active"
                        {{ old('status', $category->status ?? 'active') === 'active' ? 'selected' : '' }}>
                        Active
                    </option>

                    <option value="inactive"
                        {{ old('status', $category->status ?? '') === 'inactive' ? 'selected' : '' }}>
                        Inactive
                    </option>
                </select>
            </div>
            <div class="form_group mb-3">
                <button class="btn btn-theme">
                    {{ isset($category) ? 'Update Expense Category' : 'Add Expense Category' }}
                </button>
            </div>
        </form>
    </div>

    <div class="col-md-8">
        {{-- Status Filters --}}
        <div class="table_top_header">
            <ul class="status_list">
                <li><a href="{{ route('admin.expense-categories.index') }}" class="{{ !request('status') ? 'active' : '' }}">All ({{ $categories->total() }})</a></li>
                <li>|</li>
                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" class="{{ request('status') == 'active' ? 'active' : '' }}">Active</a></li>
                <li>|</li>
                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}" class="{{ request('status') == 'inactive' ? 'active' : '' }}">Inactive</a></li>
                <li>|</li>
                <li><a href="{{ route('admin.expense-categories.trash') }}">Trash</a></li>
            </ul>
            
            {{-- Search Form --}}
            <form method="GET" action="{{ route('admin.expense-categories.index') }}" class="search_bar ps-sm-2 d-flex gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input type="search" name="search" class="textbox" value="{{ request('search') }}" placeholder="Search Expense Categories..">
                <button type="submit" class="btn">Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.expense-categories.index') }}" class="btn">Clear</a>
                @endif
            </form>
        </div>

        {{-- Status Filter Dropdown --}}
        <div class="table_top_header">
            <form method="GET" action="{{ route('admin.expense-categories.index') }}" class="filter_bar pe-sm-2 d-flex gap-2">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <select name="status" class="select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive Only</option>
                </select>
                <button type="submit" class="btn">Filter</button>
            </form>
        </div>

        <table class="data_table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <p><strong>{{ $category->name }}</strong></p>
                        <div class="row_action">
                    <span><a href="{{ route('admin.expense-categories.index', ['edit' => $category->id]) }}">Edit</a></span>
                    <span>|</span>
                    <span><a href="{{ route('admin.expense-categories.status.toggle', $category->id) }}"
                        class="text-{{ $category->status == 'active' ? 'success' : 'danger' }}"
                        onclick="
                                event.preventDefault();
                                if(confirm('Toggle {{ $category->status }} to {{ $category->status == 'active' ? 'Inactive' : 'Active' }}?')) {
                                    document.getElementById('toggle-status-{{ $category->id }}').submit();
                                }
                        ">
                            {{ $category->status == 'active' ? 'Active' : 'Inactive' }}
                        </a><form id="toggle-status-{{ $category->id }}"
                            action="{{ route('admin.expense-categories.status.toggle', $category->id) }}"
                            method="POST"
                            style="display:none;">
                            @csrf
                            @method('PATCH')
                        </form></span>
                                            <span>|</span>
                                            <span>
                                                <a href="#" class="text-danger"
                                                onclick="event.preventDefault(); confirmDelete({{ $category->id }});">
                                                    Delete
                                                </a>

                                                <form id="delete-form-{{ $category->id }}"
                                                    action="{{ route('admin.expense-categories.destroy', $category->id) }}"
                                                    method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </span>
                                        </div>
                                        <button class="toggle_table"><img src="{{ asset('images/chevron-down.svg') }}" alt="Toggle"></button>
                    </td>
                    <td>
                        <span class="text-{{ $category->status=='active' ? 'success':'danger' }}">
                                {{ ucfirst($category->status) }}
                            </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
