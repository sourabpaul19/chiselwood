@extends('layouts.admin')
@section('content')


<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Add Item</h4>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.inventory-items.index') }}">Items</a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Items</li>
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


<form action="{{ route('admin.inventory-items.store') }}" method="POST">
    @csrf

<div class="row">
    <div class="col-sm-8">
        <div class="postbox">
            <div class="postbox_header">
                <h3>Item Information</h3>
                <a href="javascript:void(0)" class="postbox_toggle">
                <img src="{{ asset('build/assets/images/chevron-down.svg') }}">
                </a>
            </div>
            <div class="postbox_body">
                <div class="row g-3">
            
                    {{-- Item Name --}}
                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">Item Name <span class="text-danger">*</span></label>
                            <input type="text"
                                name="name"
                                class="textbox w-100 @error('name') is-invalid @enderror"
                                value="{{ old('name') }}"
                                required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- SKU --}}
                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">SKU <span class="text-danger">*</span></label>
                            <input type="text"
                                name="sku"
                                class="textbox w-100 @error('sku') is-invalid @enderror"
                                value="{{ old('sku') }}"
                                required>
                            @error('sku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Unit --}}
                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">Unit <span class="text-danger">*</span></label>
                            <select name="unit_id"
                                    class="textbox w-100 @error('unit_id') is-invalid @enderror"
                                    required>
                                <option value="">-- Select Unit --</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}"
                                        {{ old('unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->name }} ({{ $unit->short_name }})
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Categories --}}
                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">Categories <span class="text-danger">*</span></label>
                            {{-- <select name="category_ids[]"
                                    class="textbox w-100 @error('category_ids') is-invalid @enderror"
                                    multiple
                                    required> --}}
                            <select name="category_ids[]" id="categorySelect" class="textbox w-100" multiple required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ collect(old('category_ids'))->contains($cat->id) ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_ids') <div class="text-danger">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- Sub Categories --}}
                    <div class="col-md-4">
                        <div class="form_group">
                            <label class="form-label">Sub Categories</label>
                            {{-- <select name="sub_category_ids[]"
                                    class="textbox w-100"
                                    multiple> --}}
                                    <select name="sub_category_ids[]" id="subCategorySelect" class="textbox w-100" multiple>

                                @foreach($subCategories as $sub)
                                    <option value="{{ $sub->id }}"
                                        {{ collect(old('sub_category_ids'))->contains($sub->id) ? 'selected' : '' }}>
                                        {{ $sub->name }}
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
                                    <option value="{{ $vendor->id }}">
                                        {{ $vendor->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-4">
                        <div class="form_group">
                            <label class="form-label">Brand</label>
                            <select name="brand_id" class="textbox w-100">
                                <option value="">Select Brand</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Opening Stock --}}
                    <div class="col-md-3">
                        <div class="form_group">
                            <label class="form-label">Stocks</label>
                            <input type="number"
                                step="0.01"
                                name="stocks"
                                class="textbox w-100"
                                value="{{ old('stocks', 0) }}">
                        </div>
                    </div>
                    {{-- Opening Stock --}}
                    <div class="col-md-3">
                        <div class="form_group">
                            <label class="form-label">Minimum Stocks</label>
                            <input type="number"
                                step="0.01"
                                name="minimum_stock"
                                class="textbox w-100"
                                value="{{ old('minimum_stock', 0) }}">
                        </div>
                    </div>

                    {{-- Purchase Price --}}
                    <div class="col-md-3">
                        <div class="form_group">
                        <label class="form-label">Purchase Price</label>
                        <input type="number"
                                step="0.01"
                                name="purchase_price"
                                class="textbox w-100"
                                value="{{ old('purchase_price') }}">
                        </div>
                    </div>

                    {{-- Selling Price --}}
                    <div class="col-md-3">
                        <div class="form_group">
                            <label class="form-label">Selling Price</label>
                            <input type="number"
                                step="0.01"
                                name="selling_price"
                                class="textbox w-100"
                                value="{{ old('selling_price') }}">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form_group">
                            <label class="form-label">GST Rate (%)</label>
                            <input type="number" name="gst_rate" class="form-control" value="{{ old('gst_rate', 18) }}" step="0.01" min="0" max="28" required>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form_group">
                            <label class="form-label">Discount Type</label>
                            <select name="discount_type" class="form-control">
                                <option value="percent" {{ (old('discount_type', $item->discount_type ?? '') == 'percent') ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="flat" {{ (old('discount_type', $item->discount_type ?? '') == 'flat') ? 'selected' : '' }}>Flat Amount</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form_group">
                            <label class="form-label">Discount Value</label>
                            <input type="number" name="discount_value" class="form-control" min="0" step="0.01" max="100" value="{{ old('discount_value', $item->discount_value ?? 0) }}">
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="col-md-12">
                        <div class="form_group">
                            <label class="form-label">Description</label>
                            <textarea name="description"
                                    rows="3"
                                    class="textbox w-100">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    {{-- 
                    <div class="col-md-3">
                        <div class="form_group">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="textbox w-100" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success">
                        Save Item
                        </button>
                    </div>--}}
                </div>
            </div>
        </div>
    </div>
    <!-- Publish Box -->
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
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
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
{{-- Select2 --}}

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {

    /** ---------------------------
     *  INIT SELECT2
     *  ---------------------------
     */
    $('#categorySelect').select2({
        placeholder: 'Select Categories',
        width: '100%'
    });

    $('#subCategorySelect').select2({
        placeholder: 'Select Sub Categories',
        width: '100%'
    });

    /** ---------------------------
     *  LOAD SUB CATEGORIES (AJAX)
     *  ---------------------------
     */
    $('#categorySelect').on('change', function () {

        let categoryIds = $(this).val();

        // Reset sub category
        $('#subCategorySelect').html('').trigger('change');

        if (!categoryIds || categoryIds.length === 0) {
            return;
        }

        $.ajax({
            url: "{{ route('admin.inventory.get-sub-categories') }}",
            type: "GET",
            data: {
                category_ids: categoryIds
            },
            success: function (data) {

                let options = '';

                data.forEach(function (item) {
                    options += `<option value="${item.id}">${item.name}</option>`;
                });

                $('#subCategorySelect').html(options).trigger('change');
            }
        });
    });

    /** ---------------------------
     *  🚫 PREVENT OPENING SUB CATEGORY
     *  ---------------------------
     */
    $('#subCategorySelect').on('select2:opening', function (e) {

        let selectedCategories = $('#categorySelect').val();

        if (!selectedCategories || selectedCategories.length === 0) {
            alert('Please select at least one category first');
            e.preventDefault(); // ⛔ stop dropdown
        }
    });

});
</script>


@endpush