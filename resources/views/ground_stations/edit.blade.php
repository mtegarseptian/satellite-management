@extends('layouts.admin')

@section('title', 'Edit Ground Station')
@section('page-title', 'Edit Ground Station')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ground-stations.index') }}">Ground Stations</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h3 class="card-title font-weight-bold">Edit: {{ $groundStation->name }}</h3>
        </div>

        <form action="{{ route('ground-stations.update', $groundStation) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Station Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $groundStation->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="country">Country <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                   id="country" name="country" value="{{ old('country', $groundStation->country) }}" required>
                            @error('country')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Location <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('location') is-invalid @enderror" 
                           id="location" name="location" value="{{ old('location', $groundStation->location) }}" required>
                    @error('location')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="latitude">Latitude <span class="text-danger">*</span></label>
                            <input type="number" step="0.0000001" class="form-control @error('latitude') is-invalid @enderror" 
                                   id="latitude" name="latitude" value="{{ old('latitude', $groundStation->latitude) }}" required>
                            @error('latitude')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="longitude">Longitude <span class="text-danger">*</span></label>
                            <input type="number" step="0.0000001" class="form-control @error('longitude') is-invalid @enderror" 
                                   id="longitude" name="longitude" value="{{ old('longitude', $groundStation->longitude) }}" required>
                            @error('longitude')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="altitude">Altitude <small class="text-muted">(km)</small></label>
                            <input type="number" step="0.001" class="form-control @error('altitude') is-invalid @enderror" 
                                   id="altitude" name="altitude" value="{{ old('altitude', $groundStation->altitude ?? '') }}" 
                                   placeholder="e.g., 0.15">
                            @error('altitude')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Tinggi dpl (Kilometer)</small>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-2">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4">{{ old('description', $groundStation->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer bg-white text-right">
                <a href="{{ route('ground-stations.index') }}" class="btn btn-secondary">
                     Cancel
                </a>
                <button type="submit" class="btn btn-warning ml-2">
                    <i class="fas fa-edit mr-1"></i> Update Ground Station
                </button>
            </div>
        </form>
    </div>
@endsection