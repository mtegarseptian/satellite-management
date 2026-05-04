@extends('layouts.admin')

@section('title', 'Ground Stations')
@section('page-title', 'Ground Stations Management')

@section('content')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <!-- Header -->
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gray-50/50 rounded-t-2xl">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Ground Stations Catalog</h2>
                <p class="text-sm text-gray-500 mt-1">Manage global tracking facilities and infrastructure.</p>
            </div>
            <a href="{{ route('ground-stations.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center">
                <i class="fas fa-plus mr-2 text-sm"></i> Add Station
            </a>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">Station Name</th>
                        <th class="px-6 py-4 font-semibold">Location</th>
                        <th class="px-6 py-4 font-semibold">Coordinates</th>
                        <th class="px-6 py-4 font-semibold text-center">Monitored Satellites</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($groundStations as $gs)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-800">{{ $gs->name }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $gs->country }}</p>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-sm">
                                <i class="fas fa-map-marker-alt text-rose-400 mr-1"></i> {{ $gs->location }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs font-mono text-gray-600 bg-gray-100 px-2 py-1 rounded inline-block">
                                    {{ number_format($gs->latitude, 4) }}, {{ number_format($gs->longitude, 4) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-600">
                                    {{ $gs->satellites_count ?? $gs->satellites->count() }} satellites
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('ground-stations.show', $gs) }}" class="p-2 text-gray-400 hover:text-indigo-600 bg-white hover:bg-indigo-50 rounded-lg border border-transparent hover:border-indigo-100 transition-all">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('ground-stations.edit', $gs) }}" class="p-2 text-gray-400 hover:text-amber-500 bg-white hover:bg-amber-50 rounded-lg border border-transparent hover:border-amber-100 transition-all">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('ground-stations.destroy', $gs) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this ground station?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-rose-600 bg-white hover:bg-rose-50 rounded-lg border border-transparent hover:border-rose-100 transition-all">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-broadcast-tower text-4xl text-gray-300 mb-3"></i>
                                    <p>No ground stations data available.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100">
            {{ $groundStations->links() }}
        </div>
    </div>
@endsection