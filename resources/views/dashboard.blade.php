@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Overview Dashboard')

@section('content')
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Satellites</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $total_satellites }}</h3>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fas fa-satellite"></i>
                </div>
            </div>
        </div>

        <!-- Active -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Active Orbits</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $active_satellites }}</h3>
                </div>
                <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-xl flex items-center justify-center text-xl">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <!-- Inactive -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Inactive/Deorbited</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $inactive_satellites }}</h3>
                </div>
                <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-xl flex items-center justify-center text-xl">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>

        <!-- Ground Stations -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Ground Stations</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ $total_ground_stations }}</h3>
                </div>
                <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-xl">
                    <i class="fas fa-broadcast-tower"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Satellites by Orbit Type</h3>
            <div class="h-64 relative">
                <canvas id="orbitChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Top 5 Countries</h3>
            <div class="h-64 relative">
                <canvas id="countryChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-800">Recent Satellites</h3>
            <a href="{{ route('satellites.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">View all &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">Name</th>
                        <th class="px-6 py-4 font-semibold">Country</th>
                        <th class="px-6 py-4 font-semibold">Orbit Type</th>
                        <th class="px-6 py-4 font-semibold">Launch Date</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recent_satellites as $satellite)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $satellite->name }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $satellite->country }}</td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-600">{{ $satellite->orbit_type }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600">{{ $satellite->launch_date->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                @if($satellite->status == 'active')
                                    <span class="flex items-center text-emerald-600 text-sm font-medium">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span> Active
                                    </span>
                                @else
                                    <span class="flex items-center text-rose-600 text-sm font-medium">
                                        <span class="w-2 h-2 rounded-full bg-rose-500 mr-2"></span> Inactive
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No satellites found in the database.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // Orbit Chart
    const orbitData = {!! json_encode($satellites_by_orbit) !!};
    new Chart(document.getElementById('orbitChart').getContext('2d'), {
        type: 'doughnut', // Mengubah Pie ke Doughnut agar lebih modern
        data: {
            labels: orbitData.map(item => item.orbit_type),
            datasets: [{
                data: orbitData.map(item => item.count),
                backgroundColor: ['#6366f1', '#10b981', '#f59e0b'],
                borderWidth: 0,
                cutout: '70%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // Country Chart
    const countryData = {!! json_encode($satellites_by_country) !!};
    new Chart(document.getElementById('countryChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: countryData.map(item => item.country),
            datasets: [{
                label: 'Satellites',
                data: countryData.map(item => item.count),
                backgroundColor: '#6366f1',
                borderRadius: 6 // Border radius pada bar chart
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#f3f4f6' } },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endpush