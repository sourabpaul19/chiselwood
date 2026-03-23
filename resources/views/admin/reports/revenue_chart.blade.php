@extends('layouts.admin')

@section('content')

<h3>Revenue by Month</h3>
<form method="GET" class="row mb-4">
    <div class="col-md-3">
        <label>From</label>
        <input type="date" name="from"
               value="{{ $from ?? '' }}"
               class="form-control">
    </div>

    <div class="col-md-3">
        <label>To</label>
        <input type="date" name="to"
               value="{{ $to ?? '' }}"
               class="form-control">
    </div>

    <div class="col-md-2 align-self-end">
        <button class="btn btn-primary">Filter</button>
    </div>
</form>

<canvas id="revenueChart"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let labels = {!! json_encode($data->pluck('month')) !!};
let totals = {!! json_encode($data->pluck('total')) !!};

new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Revenue',
            data: totals,
            fill: false,
            tension: 0.1
        }]
    }
});
</script>

@endsection
