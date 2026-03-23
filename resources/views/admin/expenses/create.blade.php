@extends('layouts.admin')

@section('content')

<h3>Add Expense</h3>

<form method="POST" action="{{ route('admin.expenses.store') }}">
    @csrf

    <div class="mb-3">
        <label>Category</label>
        <select name="expense_category_id" class="form-control" required>
            <option value="">Select Category</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label>Date</label>
        <input type="date" name="expense_date" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Amount</label>
        <input type="number" step="0.01" name="amount" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Payment Method</label>
        <input type="text" name="payment_method" class="form-control">
    </div>

    <div class="mb-3">
        <label>Note</label>
        <textarea name="note" class="form-control"></textarea>
    </div>

    <button class="btn btn-primary">Save Expense</button>

</form>

@endsection
