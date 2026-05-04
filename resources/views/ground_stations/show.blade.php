@extends('layouts.admin')

@section('title', 'Ground Station Details')
@section('page-title', 'Facility: ' . $groundStation->name)

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri: Profil Singkat -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="h-24 bg-gradient-to-r from-purple-500 to-indigo-600"></div>
                <div class="px-6 pb-6 relative">
                    <div class="w-20 h-20 bg-white rounded-xl shadow-md border-4 border-white absolute -top-10 flex items-center justify-center text-purple-500 text-3xl">
                        <i class="fas fa-broadcast-tower"></i>
                    </div>
                    <div class="mt-12">
                        <h2 class="text-xl font-bold text-gray-800">{{ $groundStation->name }}</h2>
                        <p class="text-gray-500 mt-1"><i class="fas fa-map-marker-alt text-rose-400 mr-1"></i> {{ $groundStation->location }}, {{ $groundStation->country }}</p>
                    </div>
                    
                    <div class="mt-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-500">Latitude</span>
                            <span class="font-mono text-sm font-semibold text-gray-700">{{ number_format($groundStation->latitude, 7) }}°</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Longitude</span>
                            <span class="font-mono text-sm font-semibold text-gray-700">{{ number_format($groundStation->longitude, 7) }}°</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col space-y-3">
                <a href="{{ route('ground-stations.edit', $groundStation) }}" class="w-full py-2.5 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-lg text-center font-medium transition-colors">
                    <i class="fas fa-edit mr-2"></i> Edit Facility
                </a>
                <a href="{{ route('ground-stations.index') }}" class="w-full py-2.5 bg-gray-50 text-gray-600 hover:bg-gray-100 rounded-lg text-center font-medium transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
            </div>
            
            <!-- Description -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-sm font-bold text-gray-800 mb-3 uppercase tracking-wider">Description</h3>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $groundStation->description ?: 'No operational description available.' }}</p>
                <div class="mt-6 pt-4 border-t border-gray-100 space-y-2">
                    <p class="text-xs text-gray-400">Created: {{ $groundStation->created_at->format('d M Y') }}</p>
                    <p class="text-xs text-gray-400">Updated: {{ $groundStation->updated_at->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Relasi Satelit -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fas fa-satellite text-indigo-500 mr-2"></i> 
                        Monitored Satellites
                    </h3>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">
                        Total: {{ $groundStation->satellites->count() }}
                    </span>
                </div>
                
                <div class="p-0">
                    @if($groundStation->satellites->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-white border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wider">
                                        <th class="px-6 py-4 font-semibold">Satellite Name</th>
                                        <th class="px-6 py-4 font-semibold">Orbit</th>
                                        <th class="px-6 py-4 font-semibold">Status</th>
                                        <th class="px-6 py-4 font-semibold text-right">View</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($groundStation->satellites as $satellite)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6 py-4 font-bold text-gray-800">{{ $satellite->name }}</td>
                                            <td class="px-6 py-4">
                                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-600">{{ $satellite->orbit_type }}</span>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($satellite->status == 'active')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Active</span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">Inactive</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('satellites.show', $satellite) }}" class="p-2 text-indigo-500 hover:text-indigo-700 hover:bg-indigo-50 rounded-lg transition-all inline-block">
                                                    <i class="fas fa-arrow-right"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-16 text-gray-500">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                <i class="fas fa-inbox text-3xl text-gray-300"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-800 mb-1">No Satellites Assigned</h4>
                            <p class="text-sm">This facility is currently not monitoring any satellites.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection