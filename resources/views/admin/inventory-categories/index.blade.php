@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Inventory Categories</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Inventory Categories</li>
        </ol>
    </nav>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-sm-4 form-wrap">
    <form method="POST"
        action="{{ $category
                ? route('admin.inventory-categories.update', $category->id)
                : route('admin.inventory-categories.store') }}">

        @csrf
        @if($category)
            @method('PUT')
        @endif

        {{-- Name --}}
        <div class="form_group mb-3">
            <label class="form-label">Name</label>
            <input type="text"
                name="name"
                class="textbox w-100"
                value="{{ old('name', $category->name ?? '') }}"
                required>
        </div>

        {{-- Parent --}}
        <div class="form_group mb-3">
            <label class="form-label">Parent Category</label><br/>
            <select name="parent_id" class="select">
                <option value="">— None (Main Category) —</option>

                @include('admin.inventory-categories.partials.category-tree-options', [
                    'categories' => $categoryTree,
                    'selected'   => old('parent_id', $category->parent_id ?? null),
                    'exclude'    => $category->id ?? null,
                    'level'      => 0
                ])
            </select>
        </div>

        {{-- Status --}}
        <div class="form_group mb-3">
            <label class="form-label">Status</label><br/>
            <select name="status" class="select">
                <option value="active"
                    @selected(old('status', $category->status ?? 'active') === 'active')>
                    Active
                </option>
                <option value="inactive"
                    @selected(old('status', $category->status ?? '') === 'inactive')>
                    Inactive
                </option>
            </select>
        </div>

        <button class="btn btn-theme">
            {{ $category ? 'Update Category' : 'Add Category' }}
        </button>
    </form>
    </div>
    <div class="col-sm-8">
        {{-- Status Filters --}}
        <div class="table_top_header">
            <ul class="status_list">
                <li><a href="{{ route('admin.inventory-categories.index') }}" class="{{ !request('status') ? 'active' : '' }}">All ({{ $counts['all'] }})</a></li>
                <li>|</li>
                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" class="{{ request('status') == 'active' ? 'active' : '' }}">Active ({{ $counts['active'] }})</a></li>
                <li>|</li>
                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}" class="{{ request('status') == 'inactive' ? 'active' : '' }}">Inactive ({{ $counts['inactive'] }})</a></li>
                <li>|</li>
                <li><a href="{{ route('admin.inventory-categories.trash') }}">Trash ({{ $counts['trash'] }})</a></li>
            </ul>
            
            {{-- Search Form --}}
            <form method="GET" action="{{ route('admin.inventory-categories.index') }}" class="search_bar ps-sm-2 d-flex gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input type="search" name="search" class="textbox" value="{{ request('search') }}" placeholder="Search Categories..">
                <button type="submit" class="btn">Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.inventory-categories.index') }}" class="btn">Clear</a>
                @endif
            </form>
        </div>

        {{-- Status Filter Dropdown --}}
        <div class="table_top_header">
            <form method="GET" action="{{ route('admin.inventory-categories.index') }}" class="filter_bar pe-sm-2 d-flex gap-2">
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
                    <th>Category Name</th>
                    <th>Slug</th>
                    <th>Parent Category</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categoryList as $category)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <p><strong>{{ $category->name }}</strong></p>
                            <div class="row_action">
                    <span><a href="{{ route('admin.inventory-categories.index', ['edit' => $category->id]) }}">Edit</a></span>
                    <span>|</span>
                    <span><a href="{{ route('admin.inventory-categories.status.toggle', $category->id) }}"
                        class="text-{{ $category->status == 'active' ? 'success' : 'danger' }}"
                        onclick="
                                event.preventDefault();
                                if(confirm('Toggle {{ $category->status }} to {{ $category->status == 'active' ? 'Inactive' : 'Active' }}?')) {
                                    document.getElementById('toggle-status-{{ $category->id }}').submit();
                                }
                        ">
                            {{ $category->status == 'active' ? 'Active' : 'Inactive' }}
                        </a><form id="toggle-status-{{ $category->id }}"
                            action="{{ route('admin.inventory-categories.status.toggle', $category->id) }}"
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
                                                    action="{{ route('admin.inventory-categories.destroy', $category->id) }}"
                                                    method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </span>
                                        </div>
                                        <button class="toggle_table"><img src="{{ asset('images/chevron-down.svg') }}" alt="Toggle"></button>
                        </td>
                        <td>{{ $category->slug }}</td>
                        <td class="data-col" data-colname="Parent Category">
                            @if($category->parent)
                                {{ $category->parent->name }}
                            @else
                                <em>—</em>
                            @endif
                        </td>
                        <td>
                            <span class="text-{{ $category->status === 'active' ? 'success' : 'danger' }}">
                                {{ ucfirst($category->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted">
                            No categories found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
