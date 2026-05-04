@extends('layouts.admin')

@section('title', 'Add Ground Station')
@section('page-title', 'Register Ground Station')

@section('content')
    <div class="max-w-4xl">
        <form action="{{ route('ground-stations.store') }}" method="POST" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @csrf
            
            <div class="p-8 border-b border-gray-100 bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-800">Station Information</h3>
                <p class="text-sm text-gray-500 mt-1">Enter the geographical and operational details of the facility.</p>
            </div>

            <div class="p-8 space-y-6">
                <!-- Grid Row 1 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Station Name <span class="text-rose-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all @error('name') border-rose-500 @enderror">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-semibold text-gray-700 mb-2">Country <span class="text-rose-500">*</span></label>
                        <input type="text" id="country" name="country" value="{{ old('country') }}" required 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        @error('country') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Full Width -->
                <div>
                    <label for="location" class="block text-sm font-semibold text-gray-700 mb-2">Specific Location <span class="text-rose-500">*</span></label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" placeholder="e.g., Jakarta, Java Island" required 
                           class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                    @error('location') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Grid Row 2: Coordinates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-5 bg-gray-50 rounded-xl border border-gray-100">
                    <div>
                        <label for="latitude" class="block text-sm font-semibold text-gray-700 mb-2">Latitude <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.0000001" id="latitude" name="latitude" value="{{ old('latitude') }}" placeholder="-6.2088" required 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all font-mono text-sm">
                        <small class="text-gray-500 mt-1 block">Range: -90 to 90</small>
                    </div>

                    <div>
                        <label for="longitude" class="block text-sm font-semibold text-gray-700 mb-2">Longitude <span class="text-rose-500">*</span></label>
                        <input type="number" step="0.0000001" id="longitude" name="longitude" value="{{ old('longitude') }}" placeholder="106.8456" required 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all font-mono text-sm">
                        <small class="text-gray-500 mt-1 block">Range: -180 to 180</small>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Facility Description</label>
                    <textarea id="description" name="description" rows="4" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">{{ old('description') }}</textarea>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end space-x-3">
                <a href="{{ route('ground-stations.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 shadow-sm transition-colors flex items-center">
                    <i class="fas fa-save mr-2 text-sm"></i> Save Ground Station
                </button>
            </div>
        </form>
    </div>
@endsection