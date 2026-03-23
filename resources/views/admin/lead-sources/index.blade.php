@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Lead Source Management</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Lead Source Management</li>
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
      action="{{ isset($source)
        ? route('admin.lead-sources.update', $source)
        : route('admin.lead-sources.store') }}">

    @csrf
    @isset($source)
        @method('PUT')
    @endisset

    <h2>{{ isset($source) ? 'Edit' : 'Add' }} Lead Source</h2>

    <div class="form_group mb-3">
        <label class="form-label">Name</label>
        <input type="text"
               name="name"
               class="textbox w-100"
               value="{{ old('name', $source->name ?? '') }}"
               placeholder="Lead Source"
               required>
    </div>

    <div class="form_group mb-3">
        <label class="form-label">Status</label><br/>
        <select name="status" class="select">
            <option value="active"
                {{ old('status', $source->status ?? 'active') === 'active' ? 'selected' : '' }}>
                Active
            </option>

            <option value="inactive"
                {{ old('status', $source->status ?? '') === 'inactive' ? 'selected' : '' }}>
                Inactive
            </option>
        </select>
    </div>

    <div class="form_group mb-3">
        <button class="btn btn-theme">
            {{ isset($source) ? 'Update Lead Source' : 'Add Lead Source' }}
        </button>
    </div>
</form>

    </div>

    <div class="col-md-8">
        
        
        {{-- Status Filters --}}
        <div class="table_top_header">
            <ul class="status_list">
                <li><a href="{{ route('admin.lead-sources.index') }}" class="{{ !request('status') ? 'active' : '' }}">All ({{ $counts['all'] }})</a></li>
                <li>|</li>
                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" class="{{ request('status') == 'active' ? 'active' : '' }}">Active ({{ $counts['active'] }})</a></li>
                <li>|</li>
                <li><a href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}" class="{{ request('status') == 'inactive' ? 'active' : '' }}">Inactive ({{ $counts['inactive'] }})</a></li>
                <li>|</li>
                <li><a href="{{ route('admin.lead-sources.trash') }}">Trash ({{ $counts['trash'] }})</a></li>
            </ul>
            
            {{-- Search Form --}}
            <form method="GET" action="{{ route('admin.lead-sources.index') }}" class="search_bar ps-sm-2 d-flex gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <input type="search" name="search" class="textbox" value="{{ request('search') }}" placeholder="Search office..">
                <button type="submit" class="btn">Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.lead-sources.index') }}" class="btn">Clear</a>
                @endif
            </form>
        </div>

        {{-- Status Filter Dropdown --}}
        <div class="table_top_header">
            <form method="GET" action="{{ route('admin.lead-sources.index') }}" class="filter_bar pe-sm-2 d-flex gap-2">
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
            @foreach($sources as $source)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    <p><strong>{{ $source->name }}</strong></p>
                    <div class="row_action">
                    <span><a href="{{ route('admin.lead-sources.index', ['edit' => $source->id]) }}">Edit</a></span>
                    <span>|</span>
                    <span><a href="{{ route('admin.lead-sources.status.toggle', $source->id) }}"
                        class="text-{{ $source->status == 'active' ? 'success' : 'danger' }}"
                        onclick="
                                event.preventDefault();
                                if(confirm('Toggle {{ $source->status }} to {{ $source->status == 'active' ? 'Inactive' : 'Active' }}?')) {
                                    document.getElementById('toggle-status-{{ $source->id }}').submit();
                                }
                        ">
                            {{ $source->status == 'active' ? 'Active' : 'Inactive' }}
                        </a><form id="toggle-status-{{ $source->id }}"
                            action="{{ route('admin.lead-sources.status.toggle', $source->id) }}"
                            method="POST"
                            style="display:none;">
                            @csrf
                            @method('PATCH')
                        </form></span>
                                            <span>|</span>
                                            <span>
                                                <a href="#" class="text-danger"
                                                onclick="event.preventDefault(); confirmDelete({{ $source->id }});">
                                                    Delete
                                                </a>

                                                <form id="delete-form-{{ $source->id }}"
                                                    action="{{ route('admin.lead-sources.destroy', $source->id) }}"
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
                        <span class="text-{{ $source->status=='active' ? 'success':'danger' }}">
                            {{ ucfirst($source->status) }}
                        </span>
                    </td>
            </tr>
            @endforeach
</tbody>
        </table>
    </div>
</div>
@endsection
