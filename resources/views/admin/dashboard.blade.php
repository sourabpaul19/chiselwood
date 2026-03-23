@extends('layouts.admin')

@section('content')

<div class="section_header">
    <div class="d-flex align-items-center mb-2 justify-content-between">
        <h4 class="fw-bold">Dashboard Overview</h4>
        <!-- <div class="action_area">
            <a href="add-project.php" class="btn ms-auto">+ Project</a>
            <a href="add-client.php" class="btn ms-auto">+ Client</a>
            <a href="add-lead.php" class="btn ms-auto">+ Lead</a>
        </div> -->
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3 bg-primary text-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6>Total Projects</h6>
                    <h4 class="fw-bold">{{ $totalProjects }}</h4>
                    <small>Active: {{ $activeProjects }} | @foreach($projectStatuses as $index => $status)
                            {{ $status->name }}: {{ $status->projects_count }}
                            @if(!$loop->last)
                                | 
                            @endif
                        @endforeach

                    </small>
                </div>
                <i class="fas fa-briefcase stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 bg-success text-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6>Total Leads</h6>
                    <h4 class="fw-bold">{{ $totalLeads }}</h4>
                    <small>
                        @foreach($leadStatuses as $index => $status)
                            {{ $status->name }}: {{ $status->leads_count }}
                            @if(!$loop->last)
                                | 
                            @endif
                        @endforeach
                    </small>
                </div>
                <i class="fas fa-user-friends stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 bg-warning text-dark h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6>Revenue (Year)</h6>
                    <h4 class="fw-bold">₹15,00,000</h4>
                    <small>This Month: ₹1,20,000</small>
                </div>
                <i class="fas fa-chart-line stat-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 bg-danger text-white h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h6>Pending Payments</h6>
                    <h4 class="fw-bold">₹80,000</h4>
                    <small>Clients: ₹50,000 | Vendors: ₹30,000</small>
                </div>
                <i class="fas fa-credit-card stat-icon"></i>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card p-3">
                <h6 class="fw-bold mb-3">Revenue Trend</h6>
                <canvas id="revenueChart" height="200"></canvas>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <h6 class="fw-bold mb-3">Project Status</h6>
                <canvas id="projectChart" height="200"></canvas>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card p-3">
                <h6 class="fw-bold mb-3">Leads Breakdown</h6>
                <canvas id="leadChart" height="200"></canvas>
            </div>
        </div>

        <div class="col-md-3">
            <form action="{{ url('/gstr1/export') }}" method="GET" class="d-flex gap-2">
                <input type="date" name="from" class="form-control" required>
                <input type="date" name="to" class="form-control" required>

                <button type="submit" class="btn btn-primary">
                    📊 Export GSTR-1
                </button>
            </form>
        </div>
    </div>
</div>


<div class="row">

    <div class="col-md-3">
        <div class="card p-3 bg-primary text-white">
            <h6>Total Sales</h6>
            <h4>{{ number_format($totalSales,2) }}</h4>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-success text-white">
            <h6>Net Profit</h6>
            <h4>{{ number_format($totalProfit,2) }}</h4>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-danger text-white">
            <h6>Expenses</h6>
            <h4>{{ number_format($totalExpenses,2) }}</h4>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 bg-warning text-white">
            <h6>Outstanding</h6>
            <h4>{{ number_format($outstanding,2) }}</h4>
        </div>
    </div>
    <div class="col-md-3">
<div class="card bg-primary text-white p-3">
    <h5>Stock Value</h5>
    <h3>{{ number_format($stockValue,2) }}</h3>
</div>
</div>

</div>



    
@endsection

@push('scripts')
<script>
    // Revenue Trend
  new Chart(document.getElementById('revenueChart'), {
    type: 'line',
    data: {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
      datasets: [{
        label: 'Revenue',
        data: [200000, 250000, 220000, 300000, 280000, 320000],
        borderColor: '#007bff',
        backgroundColor: 'rgba(0,123,255,0.2)',
        fill: true
      }]
    }
  });

  // Project Status
  // Prepare dynamic data from Blade
const projectLabels = @json($projectStatuses->pluck('name'));       // ['Active', 'Completed', 'On Hold']
const projectCounts = @json($projectStatuses->pluck('projects_count')); // [12, 30, 3]
const projectColors = ['#ff7605ff', '#007bff', '#ffc107', '#28a745']; // optional: match your theme or add more colors

new Chart(document.getElementById('projectChart'), {
    type: 'doughnut',
    data: {
        labels: projectLabels,
        datasets: [{
            data: projectCounts,
            backgroundColor: projectColors,
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom', // better placement
                labels: {
                    padding: 15,
                    boxWidth: 20
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed || 0;
                        return label + ': ' + value;
                    }
                }
            }
        }
    }
});


  // Leads Breakdown
  // Prepare dynamic data from Blade
const leadLabels = @json($leadStatuses->pluck('name')); // ['Hot', 'Warm', 'Cold']
const leadCounts = @json($leadStatuses->pluck('leads_count')); // [5, 8, 12]
const leadColors = ['#dc3545', '#fd7e14', '#6c757d']; // optional custom colors

new Chart(document.getElementById('leadChart'), {
    type: 'bar',
    data: {
        labels: leadLabels,
        datasets: [{
            label: 'Leads',
            data: leadCounts,
            backgroundColor: leadColors,
            borderRadius: 5, // optional: rounded bars
            barThickness: 40, // optional: control bar width
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false // hide legend if not needed
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': ' + context.parsed.y;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

</script>
@endpush