@extends('layouts.admin')

@section('title', 'Create Ground Station')
@section('page-title', 'Create New Ground Station')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('ground-stations.index') }}">Ground Stations</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h3 class="card-title font-weight-bold">Ground Station Information</h3>
        </div>

        <form action="{{ route('ground-stations.store') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Station Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="country">Country <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                   id="country" name="country" value="{{ old('country') }}" required>
                            @error('country')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="location">Location <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('location') is-invalid @enderror" 
                           id="location" name="location" value="{{ old('location') }}" 
                           placeholder="e.g., Jakarta, Java Island" required>
                    @error('location')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="latitude">Latitude <span class="text-danger">*</span></label>
                            <input type="number" step="0.0000001" class="form-control @error('latitude') is-invalid @enderror" 
                                   id="latitude" name="latitude" value="{{ old('latitude') }}" 
                                   placeholder="e.g., -6.2088" required>
                            @error('latitude')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Range: -90 to 90</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="longitude">Longitude <span class="text-danger">*</span></label>
                            <input type="number" step="0.0000001" class="form-control @error('longitude') is-invalid @enderror" 
                                   id="longitude" name="longitude" value="{{ old('longitude') }}" 
                                   placeholder="e.g., 106.8456" required>
                            @error('longitude')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Range: -180 to 180</small>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="altitude">Altitude <small class="text-muted">(m)</small></label>
                            <div class="input-group">
                                <input type="number" step="any" class="form-control @error('altitude') is-invalid @enderror" 
                                       id="altitude" name="altitude" value="{{ old('altitude', $groundStation->altitude ?? '') }}" 
                                       placeholder="e.g., 260">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-info" onclick="fetchAltitude()" title="Ambil elevasi otomatis">
                                        <i class="fas fa-magic"></i> Auto
                                    </button>
                                </div>
                            </div>
                            @error('altitude')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                            <small class="form-text text-muted">Tinggi dpl (Meter). Klik "Auto" untuk isi via koordinat.</small>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-2">
                    <label for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="4">{{ old('description') }}</textarea>
                    @error('description')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="card-footer bg-white text-right">
                <a href="{{ route('ground-stations.index') }}" class="btn btn-secondary">
                     Cancel
                </a>
                <button type="submit" class="btn btn-primary ml-2">
                    <i class="fas fa-save mr-1"></i> Save Ground Station
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    function fetchAltitude() {
        let lat = document.getElementById('latitude').value;
        let lng = document.getElementById('longitude').value;
        let altInput = document.getElementById('altitude');

        if (!lat || !lng) {
            alert('Silakan isi Latitude dan Longitude terlebih dahulu!');
            return;
        }

        let originalValue = altInput.value;
        altInput.placeholder = 'Mencari...';
        altInput.value = '';

        let apiUrl = `https://api.open-meteo.com/v1/elevation?latitude=${lat}&longitude=${lng}`;

        fetch(apiUrl)
            .then(response => {
                if (!response.ok) throw new Error('Network response failed');
                return response.json();
            })
            .then(data => {
                if (data.elevation && data.elevation.length > 0) {
                    let altitudeInMeters = data.elevation[0];
                    // Langsung dimasukkan dalam satuan Meter (tanpa dibagi 1000)
                    altInput.value = altitudeInMeters; 
                } else {
                    altInput.value = originalValue;
                    alert('Altitude tidak ditemukan untuk koordinat tersebut.');
                }
            })
            .catch(error => {
                console.error('Error Fetching Altitude:', error);
                altInput.value = originalValue;
                alert('Gagal menghubungi server penyedia Elevasi.');
            });
    }
</script>
@endpush