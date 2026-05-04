@extends('layouts.admin')

@section('title', 'Satellite Detail')
@section('page-title', 'Details: ' . $satellite->name)

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri: Profil Singkat -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="h-24 bg-gradient-to-r from-indigo-500 to-purple-600"></div>
                <div class="px-6 pb-6 relative">
                    <div class="w-20 h-20 bg-white rounded-xl shadow-md border-4 border-white absolute -top-10 flex items-center justify-center text-indigo-500 text-3xl">
                        <i class="fas fa-satellite"></i>
                    </div>
                    <div class="mt-12">
                        <h2 class="text-xl font-bold text-gray-800">{{ $satellite->name }}</h2>
                        <p class="text-gray-500">{{ $satellite->country }}</p>
                    </div>
                    
                    <div class="mt-6 flex flex-wrap gap-2">
                        @if($satellite->status == 'active')
                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-sm font-semibold flex items-center">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span> Active
                            </span>
                        @else
                            <span class="px-3 py-1 bg-rose-100 text-rose-700 rounded-lg text-sm font-semibold flex items-center">
                                <span class="w-2 h-2 rounded-full bg-rose-500 mr-2"></span> Inactive
                            </span>
                        @endif
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg text-sm font-semibold">
                            {{ $satellite->orbit_type }} Orbit
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col space-y-3">
                <a href="{{ route('satellites.edit', $satellite) }}" class="w-full py-2.5 bg-amber-50 text-amber-600 hover:bg-amber-100 rounded-lg text-center font-medium transition-colors">
                    <i class="fas fa-edit mr-2"></i> Edit Satellite
                </a>
                <a href="{{ route('satellites.index') }}" class="w-full py-2.5 bg-gray-50 text-gray-600 hover:bg-gray-100 rounded-lg text-center font-medium transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Back to List
                </a>
            </div>
        </div>

        <!-- Kolom Kanan: Informasi Detail -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800">Mission Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8">
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Launch Date</p>
                            <p class="font-semibold text-gray-800">{{ $satellite->launch_date->format('d F Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 mb-1">Assigned Ground Station</p>
                            @if($satellite->groundStation)
                                <p class="font-semibold text-indigo-600 hover:text-indigo-800">
                                    <a href="{{ route('ground-stations.show', $satellite->groundStation) }}">
                                        <i class="fas fa-broadcast-tower mr-1 text-xs"></i> {{ $satellite->groundStation->name }}
                                    </a>
                                </p>
                            @else
                                <p class="font-semibold text-gray-400">Not Assigned</p>
                            @endif
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-sm text-gray-500 mb-1">Description</p>
                            <p class="text-gray-700 leading-relaxed">{{ $satellite->description ?: 'No description available for this satellite.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TLE Card -->
            @if($satellite->tle)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">Two-Line Element (TLE) Data</h3>
                    <button class="text-indigo-600 hover:text-indigo-800 text-sm font-medium" onclick="navigator.clipboard.writeText(document.getElementById('tle-data').innerText); alert('Copied to clipboard!')">
                        <i class="far fa-copy mr-1"></i> Copy
                    </button>
                </div>
                <div class="p-6 bg-gray-900 rounded-b-2xl overflow-x-auto">
                    <pre id="tle-data" class="text-emerald-400 font-mono text-sm tracking-widest">{{ $satellite->tle }}</pre>
                </div>
            </div>
            @endif
            
            <div class="flex justify-between text-xs text-gray-400 px-2">
                <span>Created: {{ $satellite->created_at->format('d M Y H:i') }}</span>
                <span>Last Updated: {{ $satellite->updated_at->format('d M Y H:i') }}</span>
            </div>
        </div>
    </div>
@endsection