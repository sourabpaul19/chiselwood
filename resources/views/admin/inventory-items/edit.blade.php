@extends('layouts.admin')
@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Edit Item</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.inventory-items.index') }}">Items</a></li>
            <li class="breadcrumb-item active">Edit Item</li>
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

<form action="{{ route('admin.inventory-items.update', $inventoryItem->id) }}" method="POST">
@csrf
@method('PUT')

<div class="row">
    <div class="col-sm-8">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Item Information</h3>
            </div>
            <div class="postbox_body">
                <div class="row g-3">

                    {{-- Item Name --}}
                    <div class="col-md-4">
                        <div class="form_group">
                        <label class="form-label">Item Name *</label>
                        <input type="text" name="name" class="textbox w-100"
                            value="{{ old('name', $inventoryItem->name) }}" required>
                    </div>
                    </div>

                    {{-- SKU --}}
                    <div class="col-md-4">
                        <div class="form_group">
                        <label class="form-label">SKU *</label>
                        <input type="text" name="sku" class="textbox w-100"
                            value="{{ old('sku', $inventoryItem->sku) }}" required>
                    </div>
                    </div>

                    {{-- Unit --}}
                    <div class="col-md-4">
                        <div class="form_group">
                        <label class="form-label">Unit *</label>
                        <select name="unit_id" class="textbox w-100" required>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}"
                                    {{ old('unit_id', $inventoryItem->unit_id) == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }} ({{ $unit->short_name }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    </div>

                    {{-- Categories --}}
                    <div class="col-md-6">
                        <div class="form_group">
                        <label class="form-label">Categories *</label>
                        <select name="category_ids[]" id="categorySelect" class="textbox w-100" multiple required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ $inventoryItem->categories->pluck('id')->contains($cat->id) ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    </div>

                    {{-- Sub Categories --}}
                    <div class="col-md-6">
                        <div class="form_group">
                        <label class="form-label">Sub Categories</label>
                        <select name="sub_category_ids[]" id="subCategorySelect" class="textbox w-100" multiple>
                            @foreach($subCategories as $sub)
                                <option value="{{ $sub->id }}"
                                    {{ $inventoryItem->subCategories->pluck('id')->contains($sub->id) ? 'selected' : '' }}>
                                    {{ $sub->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form_group">
                        <label class="form-label">Select Brand</label>
                        <select name="brand_id" class="textbox w-100">
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand->id }}"
                                    {{ $inventoryItem->brand_id == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>  
                    </div>

                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">Vendor</label>
                            <select name="vendor_id" class="textbox w-100">
                                <option value="">Select Vendor</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}"
                                    {{ $inventoryItem->vendor_id == $vendor->id ? 'selected' : '' }}>
                                        {{ $vendor->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Stock & Price --}}
                    <div class="col-md-3">
                        <div class="form_group">
                        <label class="form-label">Stocks</label>
                        <input type="number" step="0.01" name="stocks"
                            class="textbox w-100"
                            value="{{ old('stocks', $inventoryItem->stocks) }}">
                    </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form_group">
                        <label class="form-label">Minimum Stocks</label>
                        <input type="number" step="0.01" name="minimum_stock"
                            class="textbox w-100"
                            value="{{ old('minimum_stock', $inventoryItem->minimum_stock) }}">
                    </div>
                    </div>



                    <div class="col-md-3">
                        <div class="form_group">
                        <label class="form-label">Purchase Price</label>
                        <input type="number" step="0.01" name="purchase_price"
                            class="textbox w-100"
                            value="{{ old('purchase_price', $inventoryItem->purchase_price) }}">
                    </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form_group">
                            <label class="form-label">Selling Price</label>
                            <input type="number" step="0.01" name="selling_price"
                            class="textbox w-100"
                            value="{{ old('selling_price', $inventoryItem->selling_price) }}">
                        </div>  
                    </div>

                    <div class="col-md-3">
                        <div class="form_group">
                            <label class="form-label">GST Rate (%)</label>
                            <input type="number" name="gst_rate" class="textbox w-100" value="{{ old('gst_rate', $inventoryItem->gst_rate ?? 18) }}" step="0.01" min="0" max="28" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form_group">
                            <label class="form-label">Discount Type</label>
                            <select name="discount_type" class="textbox w-100">
                                <option value="percent" {{ (old('discount_type', $inventoryItem->discount_type ?? '') == 'percent') ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="flat" {{ (old('discount_type', $inventoryItem->discount_type ?? '') == 'flat') ? 'selected' : '' }}>Flat Amount</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form_group">
                            <label class="form-label">Discount Value</label>
                            <input type="number" name="discount_value" class="textbox w-100" min="0" step="0.01" max="100" value="{{ old('discount_value', $inventoryItem->discount_value ?? 0) }}">
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="col-md-12">
                        <div class="form_group">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" class="textbox w-100">{{ old('description', $inventoryItem->description) }}</textarea>
                    </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Publish</h3>
                <a href="javascript:void(0)" class="postbox_toggle">
                    <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
                </a>
            </div>
            <div class="postbox_body px-0 pb-0">
                <div class="form_group px-6">
                    <label>Status</label>
                    <select id="status" name="status" class="select @error('status') is-invalid @enderror">
                        <option value="active" {{ $inventoryItem->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ $inventoryItem->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="action_box">
                    {{--<a href="#" class="text-danger">Move to Trash</a>--}}
                    <input type="submit" class="btn btn-theme" value="Save">
                </div>
            </div>
        </div>
    </div>

</div>
</form>

@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(function () {

    $('#categorySelect').select2({ width: '100%' });
    $('#subCategorySelect').select2({ width: '100%' });

    $('#categorySelect').on('change', function () {

        let categoryIds = $(this).val();
        $('#subCategorySelect').empty().trigger('change');

        if (!categoryIds || categoryIds.length === 0) return;

        $.get("{{ route('admin.inventory.get-sub-categories') }}", {
            category_ids: categoryIds
        }, function (data) {

            let selected = @json($inventoryItem->subCategories->pluck('id'));

            let options = '';
            data.forEach(item => {
                options += `<option value="${item.id}" ${selected.includes(item.id) ? 'selected' : ''}>${item.name}</option>`;
            });

            $('#subCategorySelect').html(options).trigger('change');
        });
    });

    $('#subCategorySelect').on('select2:opening', function (e) {
        if (!$('#categorySelect').val()?.length) {
            alert('Select category first');
            e.preventDefault();
        }
    });

});
</script>
@endpush
