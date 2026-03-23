@extends('layouts.admin')

@section('content')

<div class="container-fluid">
    <h4 class="mb-4">📊 Daily Stock Closing Report</h4>

    <!-- Date Filter -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports.daily.closing') }}">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label>Select Date</label>
                        <input type="date" 
                               name="date" 
                               value="{{ $date }}" 
                               class="form-control">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary">
                            Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Table -->
    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Item Name</th>
                        <th>Stock on {{ $date }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($closing as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $row['item'] }}</td>
                        <td class="fw-bold">
                            {{ $row['stock'] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

</div>

@endsection
