@extends('layouts.admin')

@section('title', 'Edit Satellite')
@section('page-title', 'Update Satellite Data')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('satellites.index') }}">Satellites</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h3 class="card-title font-weight-bold">Edit: {{ $satellite->name }}</h3>
        </div>

        <form action="{{ route('satellites.update', $satellite) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Satellite Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $satellite->name) }}" required>
                            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="norad_id">NORAD ID <small class="text-muted">(Opsional untuk Global Sync)</small></label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('norad_id') is-invalid @enderror" 
                                       id="norad_id" name="norad_id" value="{{ old('norad_id', $satellite->norad_id) }}" placeholder="Contoh: 25544 untuk ISS">
                                <div class="input-group-append">
                                    <a href="https://celestrak.org/NORAD/elements/table.php" target="_blank" class="btn btn-outline-secondary" title="Cari ID Satelit di CelesTrak">
                                        <i class="fas fa-search"></i> Cari ID
                                    </a>
                                </div>
                            </div>
                            @error('norad_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            <small class="text-muted">Isi ID ini untuk update otomatis dari CelesTrak.</small>
                        </div>

                        <div class="form-group">
                            <label for="tle_url">Dynamic API URL <small class="text-muted">(Opsional)</small></label>
                            <input type="url" class="form-control @error('tle_url') is-invalid @enderror" 
                                   id="tle_url" name="tle_url" value="{{ old('tle_url', $satellite->tle_url) }}" 
                                   placeholder="Contoh: http://10.35.0.104/tle/LAPANSAT.txt">
                            @error('tle_url') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            <small class="text-muted">Isi jika satelit memiliki link TLE spesifik. (Prioritas tertinggi saat update).</small>
                        </div>

                        <div class="form-group">
                            <label for="country">Country <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                   id="country" name="country" value="{{ old('country', $satellite->country) }}" required>
                            @error('country') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="launch_date">Launch Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('launch_date') is-invalid @enderror" 
                                   id="launch_date" name="launch_date" value="{{ old('launch_date', $satellite->launch_date->format('Y-m-d')) }}" required>
                            @error('launch_date') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="orbit_type">Orbit Type <span class="text-danger">*</span></label>
                            <select class="form-control @error('orbit_type') is-invalid @enderror" id="orbit_type" name="orbit_type" required>
                                <option value="LEO" {{ (old('orbit_type', $satellite->orbit_type) == 'LEO') ? 'selected' : '' }}>LEO</option>
                                <option value="MEO" {{ (old('orbit_type', $satellite->orbit_type) == 'MEO') ? 'selected' : '' }}>MEO</option>
                                <option value="GEO" {{ (old('orbit_type', $satellite->orbit_type) == 'GEO') ? 'selected' : '' }}>GEO</option>
                            </select>
                            @error('orbit_type') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="status">Status <span class="text-danger">*</span></label>
                            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ (old('status', $satellite->status) == 'active') ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ (old('status', $satellite->status) == 'inactive') ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="ground_station_id">Ground Station</label>
                            <select class="form-control @error('ground_station_id') is-invalid @enderror" id="ground_station_id" name="ground_station_id">
                                <option value="">Select Ground Station</option>
                                @foreach($groundStations as $gs)
                                    <option value="{{ $gs->id }}" {{ (old('ground_station_id', $satellite->ground_station_id) == $gs->id) ? 'selected' : '' }}>{{ $gs->name }}</option>
                                @endforeach
                            </select>
                            @error('ground_station_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <hr>

                <div class="form-group">
                    <label>Two-Line Element (TLE) Data</label>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Line 1</span>
                        </div>
                        <input type="text" name="tle_line1" class="form-control font-weight-bold text-monospace @error('tle_line1') is-invalid @enderror" 
                               maxlength="69" value="{{ old('tle_line1', $satellite->tle_line1) }}">
                    </div>
                    @error('tle_line1') <small class="text-danger d-block mb-2">{{ $message }}</small> @enderror

                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Line 2</span>
                        </div>
                        <input type="text" name="tle_line2" class="form-control font-weight-bold text-monospace @error('tle_line2') is-invalid @enderror" 
                               maxlength="69" value="{{ old('tle_line2', $satellite->tle_line2) }}">
                    </div>
                    @error('tle_line2') <small class="text-danger d-block">{{ $message }}</small> @enderror
                </div>

                <div class="form-group">
                    <label for="image">Satellite Image</label>
                    @if($satellite->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $satellite->image) }}" alt="Thumbnail" width="100" class="rounded shadow-sm">
                        </div>
                    @endif
                    <div class="custom-file">
                        <input type="file" class="custom-file-input @error('image') is-invalid @enderror" id="image" name="image">
                        <label class="custom-file-label" for="image">Choose new file...</label>
                    </div>
                    @error('image') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $satellite->description) }}</textarea>
                    @error('description') <span class="invalid-feedback">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="card-footer bg-white text-right">
                <a href="{{ route('satellites.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-warning px-4">
                    <i class="fas fa-edit mr-1"></i> Update Satellite
                </button>
            </div>
        </form>
    </div>
@endsection