@extends('layouts.admin')

@section('title', 'Edit Ground Station')
@section('page-title', 'Edit: ' . $groundStation->name)

@section('content')
    <div class="max-w-4xl">
        <form action="{{ route('ground-stations.update', $groundStation) }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @csrf
            @method('PUT')
            
            <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Update Station Data</h3>
                    <p class="text-sm text-gray-500 mt-1">Modify the geographical and operational details.</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">
                    ID: {{ $groundStation->id }}
                </span>
            </div>

            <div class="p-8 space-y-6">
                <!-- Grid Row 1 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Station Name <span class="text-rose-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $groundStation->name) }}" required 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all @error('name') border-rose-500 @enderror">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-semibold text-gray-700 mb-2">Country <span class="text-rose-500">*</span></label>
                        <input type="text" id="country" name="country" value="{{ old('country', $groundStation->country) }}" required 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        @error('country') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Full Width -->
                <div>
                    <label for="location" class="block text-sm font-semibold text-gray-700 mb-2">Specific Location <span class="text-rose-500">*</span></label>
                    <input type="text" id="location" name="location" value="{{ old('location', $groundStation->location) }}" required 
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                    @error('location') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Grid Row 2: Coordinates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-gray-50 rounded-xl border border-gray-100">
                    <div>
                        <label for="latitude" class="block text-sm font-semibold text-gray-700 mb-2">Latitude <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.0000001" id="latitude" name="latitude" value="{{ old('latitude', $groundStation->latitude) }}" required 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all font-mono text-sm">
                    </div>

                    <div>
                        <label for="longitude" class="block text-sm font-semibold text-gray-700 mb-2">Longitude <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.0000001" id="longitude" name="longitude" value="{{ old('longitude', $groundStation->longitude) }}" required 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all font-mono text-sm">
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Facility Description</label>
                    <textarea id="description" name="description" rows="4" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">{{ old('description', $groundStation->description) }}</textarea>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end space-x-3">
                <a href="{{ route('ground-stations.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 shadow-sm transition-colors flex items-center">
                    <i class="fas fa-save mr-2 text-sm"></i> Update Changes
                </button>
            </div>
        </form>
    </div>
@endsection