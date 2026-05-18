@extends('layouts.admin')

@section('title', 'Statistics')
@section('page-title', 'Satellite Statistics')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Statistics</li>
@endsection

@section('content')
    <!-- Summary Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $total_satellites }}</h3>
                    <p>Total Satellites</p>
                </div>
                <div class="icon">
                    <i class="fas fa-satellite"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $active_satellites }}</h3>
                    <p>Active Satellites</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $inactive_satellites }}</h3>
                    <p>Inactive Satellites</p>
                </div>
                <div class="icon">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $total_ground_stations }}</h3>
                    <p>Ground Stations</p>
                </div>
                <div class="icon">
                    <i class="fas fa-broadcast-tower"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row">
        <!-- Satellites by Orbit Type -->
        <div class="col-md-6">
            <div class="card" style="min-height: 420px;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Satellites by Orbit Type
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="orbitChart" style="min-height: 300px; height: 300px; max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Satellites by Status -->
        <div class="col-md-6">
            <div class="card" style="min-height: 420px;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Satellites by Status
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" style="min-height: 300px; height: 300px; max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row">
        <!-- Satellites by Country -->
        <div class="col-md-6">
            <div class="card" style="min-height: 420px;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Satellites by Country
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="countryChart" style="min-height: 300px; height: 300px; max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- Ground Stations by Country -->
        <div class="col-md-6">
            <div class="card" style="min-height: 420px;">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Ground Stations by Country
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="gsCountryChart" style="min-height: 300px; height: 300px; max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Launches -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line mr-1"></i>
                        Monthly Launches ({{ date('Y') }})
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" style="min-height: 250px; height: 250px; max-height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables -->
    <div class="row">
        <div class="col-md-6">
            <div class="card" style="min-height: 300px;">
                <div class="card-header">
                    <h3 class="card-title">Satellites by Country (Detailed)</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Country</th>
                                <th>Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($satellites_by_country as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->country }}</td>
                                    <td>{{ $item->count }}</td>
                                    <td>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-primary" 
                                                 style="width: {{ ($item->count / $total_satellites) * 100 }}%">
                                            </div>
                                        </div>
                                        <small>{{ number_format(($item->count / $total_satellites) * 100, 1) }}%</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card" style="min-height: 300px;">
                <div class="card-header">
                    <h3 class="card-title">Satellites by Orbit Type (Detailed)</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Orbit Type</th>
                                <th>Count</th>
                                <th>Percentage</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($satellites_by_orbit as $item)
                                <tr>
                                    <td>
                                        <span class="badge badge-info">{{ $item->orbit_type }}</span>
                                    </td>
                                    <td>{{ $item->count }}</td>
                                    <td>
                                        <div class="progress progress-xs">
                                            <div class="progress-bar bg-info" 
                                                 style="width: {{ ($item->count / $total_satellites) * 100 }}%">
                                            </div>
                                        </div>
                                        <small>{{ number_format(($item->count / $total_satellites) * 100, 1) }}%</small>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No data available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Orbit Type Chart (Pie Chart)
    const orbitData = {!! json_encode($satellites_by_orbit) !!};
    const orbitLabels = orbitData.map(item => item.orbit_type);
    const orbitCounts = orbitData.map(item => item.count);

    new Chart(document.getElementById('orbitChart'), {
        type: 'pie',
        data: {
            labels: orbitLabels,
            datasets: [{
                data: orbitCounts,
                backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Status Chart (Doughnut Chart)
    const statusData = {!! json_encode($satellites_by_status) !!};
    const statusLabels = statusData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1));
    const statusCounts = statusData.map(item => item.count);

    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusCounts,
                backgroundColor: ['#28a745', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Satellites by Country Chart (Bar Chart)
    const countryData = {!! json_encode($satellites_by_country) !!};
    const countryLabels = countryData.map(item => item.country);
    const countryCounts = countryData.map(item => item.count);

    new Chart(document.getElementById('countryChart'), {
        type: 'bar',
        data: {
            labels: countryLabels,
            datasets: [{
                label: 'Satellites',
                data: countryCounts,
                backgroundColor: '#007bff',
                borderColor: '#0056b3',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { 
                        stepSize: 1,
                        precision: 0
                    }
                }
            }
        }
    });

    // Ground Stations by Country Chart (Horizontal Bar)
    const gsCountryData = {!! json_encode($ground_stations_by_country) !!};
    const gsCountryLabels = gsCountryData.map(item => item.country);
    const gsCountryCounts = gsCountryData.map(item => item.count);

    new Chart(document.getElementById('gsCountryChart'), {
        type: 'bar',
        data: {
            labels: gsCountryLabels,
            datasets: [{
                label: 'Ground Stations',
                data: gsCountryCounts,
                backgroundColor: '#dc3545',
                borderColor: '#a71d2a',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: { 
                        stepSize: 1,
                        precision: 0
                    }
                }
            }
        }
    });

    // Monthly Launches Chart (Line Chart)
    const monthlyData = {!! json_encode($monthly_launches) !!};
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    // Create array untuk semua bulan (1-12)
    let monthlyChartData = new Array(12).fill(0);
    monthlyData.forEach(item => {
        monthlyChartData[item.month - 1] = item.count;
    });

    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: monthNames,
            datasets: [{
                label: 'Launches',
                data: monthlyChartData,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { 
                        stepSize: 1,
                        precision: 0
                    }
                }
            }
        }
    });
</script>
@endpush