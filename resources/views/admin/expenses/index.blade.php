@extends('layouts.admin')

@section('content')


<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4>Expenses Management</h4>
        <div class="action_area">
            <a href="{{ route('admin.expenses.create') }}" class="btn ms-auto">Add Expense</a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Expenses Management</li>
        </ol>
    </nav>
</div>

<table class="data_table">
    <thead>
        <tr>
            <th>#</th>
            <th>Number</th>
            <th>Date</th>
            <th>Category</th>
            <th>Amount</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($expenses as $expense)
        <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $expense->expense_number }}</td>
            <td>{{ $expense->expense_date }}</td>
            <td>{{ $expense->category->name }}</td>
            <td>{{ number_format($expense->amount,2) }}</td>
            <td>
                <form method="POST" action="{{ route('admin.expenses.destroy', $expense) }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn text-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{ $expenses->links() }}

@endsection
