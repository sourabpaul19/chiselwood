@extends('layouts.admin')

@section('content')

<canvas id="profitChart"></canvas>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('profitChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($monthly->pluck('month')) !!},
        datasets: [{
            label: 'Revenue',
            data: {!! json_encode($monthly->pluck('revenue')) !!}
        }]
    }
});
</script>


@endsection
