@extends('layouts.admin')

@section('title', 'Edit Satellite')
@section('page-title', 'Edit: ' . $satellite->name)

@section('content')
    <div class="max-w-4xl">
        <form action="{{ route('satellites.update', $satellite) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            @csrf
            @method('PUT')
            
            <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Update Satellite Data</h3>
                    <p class="text-sm text-gray-500 mt-1">Modify the technical parameters of the satellite.</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700">
                    ID: {{ $satellite->id }}
                </span>
            </div>

            <div class="p-8 space-y-6">
                <!-- Grid Row 1 -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Satellite Name <span class="text-rose-500">*</span></label>
                        <input type="text" id="name" name="name" value="{{ old('name', $satellite->name) }}" required 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all @error('name') border-rose-500 @enderror">
                        @error('name') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="country" class="block text-sm font-semibold text-gray-700 mb-2">Country of Origin <span class="text-rose-500">*</span></label>
                        <input type="text" id="country" name="country" value="{{ old('country', $satellite->country) }}" required 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        @error('country') <span class="text-xs text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Grid Row 2 -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="launch_date" class="block text-sm font-semibold text-gray-700 mb-2">Launch Date <span class="text-rose-500">*</span></label>
                        <input type="date" id="launch_date" name="launch_date" value="{{ old('launch_date', $satellite->launch_date->format('Y-m-d')) }}" required 
                               class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                    </div>

                    <div>
                        <label for="orbit_type" class="block text-sm font-semibold text-gray-700 mb-2">Orbit Type <span class="text-rose-500">*</span></label>
                        <select id="orbit_type" name="orbit_type" required class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all bg-white">
                            <option value="LEO" {{ old('orbit_type', $satellite->orbit_type) == 'LEO' ? 'selected' : '' }}>LEO (Low Earth)</option>
                            <option value="MEO" {{ old('orbit_type', $satellite->orbit_type) == 'MEO' ? 'selected' : '' }}>MEO (Medium Earth)</option>
                            <option value="GEO" {{ old('orbit_type', $satellite->orbit_type) == 'GEO' ? 'selected' : '' }}>GEO (Geostationary)</option>
                        </select>
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status <span class="text-rose-500">*</span></label>
                        <select id="status" name="status" required class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all bg-white">
                            <option value="active" {{ old('status', $satellite->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $satellite->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-6 mt-6"></div>

                <!-- Detail Textarea -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description / Mission Notes</label>
                    <textarea id="description" name="description" rows="3" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">{{ old('description', $satellite->description) }}</textarea>
                </div>

                <!-- TLE Data -->
                <div>
                    <label for="tle" class="block text-sm font-semibold text-gray-700 mb-2">TLE (Two-Line Element)</label>
                    <textarea id="tle" name="tle" rows="3" class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all font-mono text-sm bg-gray-50">{{ old('tle', $satellite->tle) }}</textarea>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex justify-end space-x-3">
                <a href="{{ route('satellites.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 shadow-sm transition-colors flex items-center">
                    <i class="fas fa-save mr-2 text-sm"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
@endsection