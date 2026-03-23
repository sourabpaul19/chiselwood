@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Inventory Item Management</h4>
        <div class="action_area">
            <a href="{{ route('admin.inventory-items.create') }}" class="btn ms-auto">Add New Item</a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Inventory Item Management</li>
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


{{-- Status Filters --}}
<div class="table_top_header">
    <ul class="status_list">
        <li><a href="{{ route('admin.inventory-items.index') }}" class="{{ !request('status') ? 'active' : '' }}">All ({{ $items->total() }})</a></li>
        <li>|</li>
        <li><a href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}" class="{{ request('status') == 'active' ? 'active' : '' }}">Active</a></li>
        <li>|</li>
        <li><a href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}" class="{{ request('status') == 'inactive' ? 'active' : '' }}">Inactive</a></li>
        <li>|</li>
        <li><a href="{{ route('admin.inventory-items.trash') }}">Trash</a></li>
    </ul>
    
    {{-- Search Form --}}
    <form method="GET" action="{{ route('admin.inventory-items.index') }}" class="search_bar ps-sm-2 d-flex gap-2">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <input type="search" name="search" class="textbox" value="{{ request('search') }}" placeholder="Search Item..">
        <button type="submit" class="btn">Search</button>
        @if(request('search'))
            <a href="{{ route('admin.inventory-items.index') }}" class="btn">Clear</a>
        @endif
    </form>
</div>

{{-- Status Filter Dropdown --}}
<div class="table_top_header">
    <form method="GET" action="{{ route('admin.inventory-items.index') }}" class="filter_bar pe-sm-2 d-flex gap-2">
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        <select name="status" class="select" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Only</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive Only</option>
        </select>

        <select name="category_id" class="select" onchange="this.form.submit()">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}"
                    {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>

        <select name="sub_category_id" class="select" onchange="this.form.submit()">
            <option value="">All Sub Categories</option>
            @foreach($subCategories as $sub)
                <option value="{{ $sub->id }}"
                    {{ request('sub_category_id') == $sub->id ? 'selected' : '' }}>
                    {{ $sub->name }}
                </option>
            @endforeach
        </select>

        <select name="brand_id" class="select" onchange="this.form.submit()">
            <option value="">All Brands</option>
            @foreach($brands as $brand)
                <option value="{{ $brand->id }}"
                    {{ request('brand_id')==$brand->id?'selected':'' }}>
                    {{ $brand->name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn">Filter</button>
    </form>
</div>

<table class="data_table">
    <thead>
        <tr>
            <th>#</th>
            <th>Item</th>
            <th>SKU</th>
            <th>Category</th>
            <th>Sub Category</th>
            <th>Brand</th>
            <th>Vendor</th>
            <th>Unit</th>
            <th>Stocks</th>
            <th>Purchase</th>
            <th>Selling</th>
            <th>Dis. Type</th>
            <th>Dis. Amount</th>
            <th>GST</th>
        </tr>
    </thead>
    <tbody>
    @forelse($items as $item)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>
                <p><strong>{{ $item->name }}</strong>
                @if($item->subCategory)
                    <br>
                    <small class="text-muted">
                        {{ $item->subCategory->name }}
                    </small>
                @endif</p>
                <div class="row_action">
                    <span><a href="{{ route('admin.inventory-items.show', $item->id) }}">View</a></span>
                    <span>|</span>
                    <span><a href="{{ route('admin.inventory-items.edit', $item->id) }}">Edit</a></span>
                    <span>|</span>
                    <span><a href="javascript:void(0)"
   class="text-{{ $item->status === 'active' ? 'success' : 'danger' }}"
   onclick="event.preventDefault();
            if(confirm('Toggle status?')) {
                document.getElementById('toggle-status-{{ $item->id }}').submit();
            }">
    {{ ucfirst($item->status) }}
</a>

<form id="toggle-status-{{ $item->id }}"
      action="{{ route('admin.inventory-items.status.toggle', $item->id) }}"
      method="POST"
      style="display:none;">
    @csrf
</form>


</span>
                                            <span>|</span>
                                            <span>
                                                <a href="#" class="text-danger"
                                                onclick="event.preventDefault(); confirmDelete({{ $item->id }});">
                                                    Delete
                                                </a>

                                                <form id="delete-form-{{ $item->id }}"
                                                    action="{{ route('admin.inventory-items.destroy', $item->id) }}"
                                                    method="POST"
                                                    style="display:none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </span>
                                            <span>|</span>
                                            <span><a href="{{ route('admin.reports.inventory.movement', $item->id) }}" 
       class="text-success">
        Inventory Movements
    </a></span>
                                        </div>
                                        <button class="toggle_table"><img src="{{ asset('images/chevron-down.svg') }}" alt="Toggle"></button>
            </td>
            <td>{{ $item->sku }}</td>
            <td>
                
                @foreach($item->parentCategories as $category)
                    <span class="badge bg-primary">{{ $category->name }}</span>
                @endforeach


            </td>
            <td>
                @foreach($item->subCategories as $sub)
                    <span class="badge bg-info">{{ $sub->name }}</span>
                @endforeach
            </td>
            <td>{{ $item->brand->name ?? '-' }}</td>
            <td>{{ $item->vendor->user->name ?? '-' }}</td>
            <td>
                {{ $item->unit->name ?? '-' }}
                <small class="text-muted">
                    ({{ $item->unit->short_name ?? '' }})
                </small>
            </td>
            <td>
                <span class="fw-bold">
                    {{ number_format($item->stocks, 2) }}
                </span>
            </td>
            <td>₹ {{ number_format($item->purchase_price, 2) }}</td>
            <td>₹ {{ number_format($item->selling_price, 2) }}</td>
            <td>{{ $item->discount_type ?? '-' }}</td>
            <td>{{ $item->discount_value ?? '-' }}</td>
            <td>{{ $item->gst_rate ?? '-' }}%</td>
        </tr>
    @empty
        <tr>
            <td colspan="10" class="text-center text-muted">
                No inventory items found
            </td>
        </tr>
    @endforelse
    </tbody>
</table>

{{ $items->withQueryString()->links() }}

@endsection