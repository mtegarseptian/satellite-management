@extends('layouts.admin')

@section('title', 'Satellites Catalog')
@section('page-title', 'Satellites Management')

@section('content')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <!-- Header & Search -->
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <h2 class="text-xl font-bold text-gray-800">Satellite Catalog</h2>
            <a href="{{ route('satellites.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium transition-colors shadow-sm flex items-center">
                <i class="fas fa-plus mr-2 text-sm"></i> Add Satellite
            </a>
        </div>

        <div class="p-6 bg-gray-50/50 border-b border-gray-100">
            <form method="GET" action="{{ route('satellites.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="col-span-1 md:col-span-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" class="w-full pl-10 pr-3 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none text-sm transition-all" placeholder="Search satellite name..." value="{{ request('search') }}">
                </div>
                
                <select name="orbit" class="w-full py-2 px-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm bg-white">
                    <option value="">All Orbits</option>
                    <option value="LEO" {{ request('orbit') == 'LEO' ? 'selected' : '' }}>LEO</option>
                    <option value="MEO" {{ request('orbit') == 'MEO' ? 'selected' : '' }}>MEO</option>
                    <option value="GEO" {{ request('orbit') == 'GEO' ? 'selected' : '' }}>GEO</option>
                </select>

                <select name="status" class="w-full py-2 px-3 border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none text-sm bg-white">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <div class="flex space-x-2">
                    <button type="submit" class="flex-1 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm">
                        Filter
                    </button>
                    <a href="{{ route('satellites.index') }}" class="px-3 bg-white border border-gray-200 text-gray-500 hover:text-rose-500 hover:bg-rose-50 rounded-lg flex items-center justify-center transition-colors" title="Reset Filters">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">Name & Country</th>
                        <th class="px-6 py-4 font-semibold">Orbit</th>
                        <th class="px-6 py-4 font-semibold">Launch Date</th>
                        <th class="px-6 py-4 font-semibold">Ground Station</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($satellites as $satellite)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <p class="font-bold text-gray-800">{{ $satellite->name }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $satellite->country }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-600">{{ $satellite->orbit_type }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-sm">{{ $satellite->launch_date->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $satellite->groundStation ? $satellite->groundStation->name : '-' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($satellite->status == 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('satellites.show', $satellite) }}" class="p-2 text-gray-400 hover:text-indigo-600 bg-white hover:bg-indigo-50 rounded-lg border border-transparent hover:border-indigo-100 transition-all">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('satellites.edit', $satellite) }}" class="p-2 text-gray-400 hover:text-amber-500 bg-white hover:bg-amber-50 rounded-lg border border-transparent hover:border-amber-100 transition-all">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('satellites.destroy', $satellite) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?')">
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
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-satellite text-4xl text-gray-300 mb-3"></i>
                                    <p>No satellites data available.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Tailwind Override (jika paginate bawaan aktif) -->
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $satellites->links() }}
        </div>
    </div>
@endsection