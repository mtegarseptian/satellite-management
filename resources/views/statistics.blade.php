@extends('layouts.admin')

@section('title', 'Analytics & Statistics')
@section('page-title', 'Satellite Analytics')

@section('content')
    <!-- Top Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 border-l-4 border-l-indigo-500">
            <p class="text-sm font-medium text-gray-500 mb-1">Total Monitored</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ $total_satellites }}</h3>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 border-l-4 border-l-emerald-500">
            <p class="text-sm font-medium text-gray-500 mb-1">Active Status</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ $active_satellites }}</h3>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 border-l-4 border-l-amber-500">
            <p class="text-sm font-medium text-gray-500 mb-1">Inactive/Deorbited</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ $inactive_satellites }}</h3>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 border-l-4 border-l-rose-500">
            <p class="text-sm font-medium text-gray-500 mb-1">Ground Stations</p>
            <h3 class="text-3xl font-bold text-gray-800">{{ $total_ground_stations }}</h3>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Orbit & Status -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-4">Distribution by Orbit Type</h3>
            <div class="h-64 relative">
                <canvas id="orbitChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-4">Current Status Ratio</h3>
            <div class="h-64 relative">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Geographic Charts -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-4">Satellites per Country</h3>
            <div class="h-72 relative">
                <canvas id="countryChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-4">Ground Stations Geography</h3>
            <div class="h-72 relative">
                <canvas id="gsCountryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Monthly Line Chart -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <h3 class="text-lg font-bold text-gray-800 mb-6 border-b border-gray-100 pb-4">Launches Trend ({{ date('Y') }})</h3>
        <div class="h-80 relative">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Common settings for styling
    Chart.defaults.font.family = "'Inter', 'system-ui', 'sans-serif'";
    Chart.defaults.color = '#6b7280';

    // Orbit Type (Doughnut)
    const orbitData = {!! json_encode($satellites_by_orbit) !!};
    new Chart(document.getElementById('orbitChart'), {
        type: 'doughnut',
        data: {
            labels: orbitData.map(item => item.orbit_type),
            datasets: [{
                data: orbitData.map(item => item.count),
                backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ec4899', '#8b5cf6'],
                borderWidth: 0,
                cutout: '65%'
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
    });

    // Status (Pie)
    const statusData = {!! json_encode($satellites_by_status) !!};
    new Chart(document.getElementById('statusChart'), {
        type: 'pie',
        data: {
            labels: statusData.map(item => item.status.charAt(0).toUpperCase() + item.status.slice(1)),
            datasets: [{
                data: statusData.map(item => item.count),
                backgroundColor: ['#10b981', '#f43f5e'],
                borderWidth: 0
            }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
    });

    // Satellites Country (Bar)
    const countryData = {!! json_encode($satellites_by_country) !!};
    new Chart(document.getElementById('countryChart'), {
        type: 'bar',
        data: {
            labels: countryData.map(item => item.country),
            datasets: [{
                label: 'Satellites',
                data: countryData.map(item => item.count),
                backgroundColor: '#6366f1',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { color: '#f3f4f6' } }, x: { grid: { display: false } } }
        }
    });

    // GS Country (Horizontal Bar)
    const gsCountryData = {!! json_encode($ground_stations_by_country) !!};
    new Chart(document.getElementById('gsCountryChart'), {
        type: 'bar',
        data: {
            labels: gsCountryData.map(item => item.country),
            datasets: [{
                label: 'Ground Stations',
                data: gsCountryData.map(item => item.count),
                backgroundColor: '#8b5cf6',
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } },
            scales: { x: { beginAtZero: true, grid: { color: '#f3f4f6' } }, y: { grid: { display: false } } }
        }
    });

    // Monthly Launches (Line)
    const monthlyData = {!! json_encode($monthly_launches) !!};
    let monthlyChartData = new Array(12).fill(0);
    monthlyData.forEach(item => { monthlyChartData[item.month - 1] = item.count; });

    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Launches',
                data: monthlyChartData,
                borderColor: '#6366f1',
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#6366f1',
                pointBorderWidth: 2,
                pointRadius: 4
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true, grid: { borderDash: [4, 4], color: '#f3f4f6' } }, x: { grid: { display: false } } }
        }
    });
</script>
@endpush