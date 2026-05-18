@extends('layouts.admin')

@section('title', 'Satellite Details')
@section('page-title', 'Satellite Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('satellites.index') }}">Satellites</a></li>
    <li class="breadcrumb-item active">{{ $satellite->name }}</li>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #satelliteMap { width: 100%; z-index: 1; border-radius: 0 0 4px 4px; background: #0b101d; }

        /* Mencegah konflik CSS gambar AdminLTE */
        .leaflet-container img {
            max-width: none !important;
            max-height: none !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Wrapper Marker */
        .satellite-marker-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transform: translate(-50%, -10px); 
        }

        /* Animasi titik berkedip */
        .blinking-dot {
            width: 16px;
            height: 16px;
            background-color: #ff3333; 
            border-radius: 50%;
            border: 2px solid #ffffff;
            box-shadow: 0 0 0 rgba(255, 51, 51, 0.7);
            animation: pulse-warning 1.5s infinite;
        }

        /* Nama Satelit */
        .satellite-label {
            margin-top: 6px;
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            color: #333;
            border: 1px solid #ccc;
            white-space: nowrap;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        @keyframes pulse-warning {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 51, 51, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 15px rgba(255, 51, 51, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 51, 51, 0); }
        }
    </style>
@endpush

@section('content')
    <div class="row">
        <div class="col-md-4 d-flex flex-column">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title">Satellite Image</h3>
                </div>
                <div class="card-body text-center d-flex align-items-center justify-content-center" style="min-height: 250px;">
                    @if($satellite->image)
                        <img src="{{ asset('storage/' . $satellite->image) }}" alt="{{ $satellite->name }}" class="img-fluid rounded" style="max-height: 230px; object-fit: cover;">
                    @else
                        <div class="text-muted">
                            <i class="fas fa-satellite fa-5x"></i>
                            <p class="mt-3 mb-0">No image available</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm flex-grow-1 mb-3">
                <div class="card-header">
                    <h3 class="card-title">Information</h3>
                    <div class="card-tools">
                        <a href="{{ route('satellites.edit', $satellite) }}" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped m-0">
                        <tr><th width="40%" class="pl-4">Name:</th><td>{{ $satellite->name }}</td></tr>
                        <tr><th class="pl-4">Country:</th><td>{{ $satellite->country }}</td></tr>
                        <tr><th class="pl-4">Launch Date:</th><td>{{ $satellite->launch_date->format('d M Y') }}</td></tr>
                        <tr><th class="pl-4">Orbit Type:</th><td><span class="badge badge-info">{{ $satellite->orbit_type }}</span></td></tr>
                        <tr><th class="pl-4">Status:</th>
                            <td>
                                @if($satellite->status == 'active') <span class="badge badge-success">Active</span>
                                @else <span class="badge badge-danger">Inactive</span> @endif
                            </td>
                        </tr>
                        <tr><th class="pl-4">Ground Station:</th>
                            <td>
                                @if($satellite->groundStation) {{ $satellite->groundStation->name }}
                                @else <span class="text-muted">Not assigned</span> @endif
                            </td>
                        </tr>
                        <tr>
                            <th class="pl-4 align-top pt-3">TLE Data:</th>
                            <td class="pt-3 pr-3">
                                @if($satellite->tle_line1 && $satellite->tle_line2)
                                    <div class="mb-1" style="font-family: inherit;">{{ $satellite->tle_line1 }}</div>
                                    <div style="font-family: inherit;">{{ $satellite->tle_line2 }}</div>
                                    <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle"></i> Format standar SGP4</small>
                                @else
                                    <span class="text-muted font-italic">Data TLE belum tersedia</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-8 d-flex flex-column">
            <div class="row">
                <div class="col-md-4 col-sm-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-info"><i class="fas fa-arrows-alt-v"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Latitude</span>
                            <span class="info-box-number" id="liveLat">--.----</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-success"><i class="fas fa-arrows-alt-h"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Longitude</span>
                            <span class="info-box-number" id="liveLng">--.----</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6 col-12">
                    <div class="info-box shadow-sm">
                        <span class="info-box-icon bg-warning"><i class="fas fa-layer-group"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Altitude</span>
                            <span class="info-box-number"><span id="liveAlt">---.--</span> km</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm flex-grow-1 d-flex flex-column mb-3">
                <div class="card-header bg-dark">
                    <h3 class="card-title mt-1"><i class="fas fa-map-marked-alt text-warning mr-2"></i> Live Orbit Tracker</h3>
                    <div class="card-tools">
                        <button class="btn btn-sm btn-outline-light" onclick="recenterMap()">
                            <i class="fas fa-crosshairs"></i> Recenter
                        </button>
                    </div>
                </div>
                <div class="card-body p-0 d-flex flex-column flex-grow-1">
                    <div id="satelliteMap" class="flex-grow-1" style="min-height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/satellite.js/4.0.0/satellite.min.js"></script>
    <script>
        let map;
        let lastKnownPos = [0, 0];

        document.addEventListener('DOMContentLoaded', function () {
            
            var bounds = [[-90, -Infinity], [90, Infinity]];
            map = L.map('satelliteMap', {
                minZoom: 1.5,
                maxBounds: bounds,
                maxBoundsViscosity: 1.0,
                worldCopyJump: true,
                zoomSnap: 0.5,
                zoomDelta: 0.5
            }).setView([0, 0], 2.5);
            
            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri', maxZoom: 18
            }).addTo(map);

            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 18
            }).addTo(map);

            var satName = "{{ $satellite->name }}";
            var tle1 = "{{ $satellite->tle_line1 }}";
            var tle2 = "{{ $satellite->tle_line2 }}";

            var customIcon = L.divIcon({
                className: 'custom-satellite-icon', 
                html: `
                    <div class="satellite-marker-wrapper">
                        <div class="blinking-dot"></div>
                        <div class="satellite-label" style="border-bottom: 2px solid #ff3333;">${satName}</div>
                    </div>
                `,
                iconSize: [0, 0], iconAnchor: [0, 0] 
            });

            var satMarker = L.marker([0, 0], {icon: customIcon}).addTo(map);
            var orbitLine = L.polyline([], {color: '#ff3333', weight: 2, opacity: 0.8}).addTo(map);

            let isFirstLoad = true;
            var satrec = satellite.twoline2satrec(tle1, tle2);

            function updateLiveTelemetry() {
                const now = new Date();
                const positionAndVelocity = satellite.propagate(satrec, now);
                const gmst = satellite.gstime(now);

                if (positionAndVelocity.position) {
                    const positionGd = satellite.eciToGeodetic(positionAndVelocity.position, gmst);
                    
                    const lat = satellite.degreesLat(positionGd.latitude);
                    const lng = satellite.degreesLong(positionGd.longitude);
                    const alt = positionGd.height;

                    lastKnownPos = [lat, lng];

                    document.getElementById('liveLat').innerText = lat.toFixed(4) + '°';
                    document.getElementById('liveLng').innerText = lng.toFixed(4) + '°';
                    document.getElementById('liveAlt').innerText = alt.toFixed(2);

                    const newLatLng = new L.LatLng(lat, lng);
                    satMarker.setLatLng(newLatLng);
                    
                    let pathSegments = [];
                    let currentSegment = [];
                    let lastLng = null;

                    for (let i = -45; i <= 45; i += 1) {
                        let calcTime = new Date(now.getTime() + i * 60000);
                        let calcPV = satellite.propagate(satrec, calcTime);
                        
                        if (calcPV.position) {
                            let calcGd = satellite.eciToGeodetic(calcPV.position, satellite.gstime(calcTime));
                            let pLat = satellite.degreesLat(calcGd.latitude);
                            let pLng = satellite.degreesLong(calcGd.longitude);

                            if (lastLng !== null && Math.abs(pLng - lastLng) > 180) {
                                pathSegments.push(currentSegment);
                                currentSegment = [];
                            }

                            currentSegment.push([pLat, pLng]);
                            lastLng = pLng;
                        }
                    }
                    if (currentSegment.length > 0) pathSegments.push(currentSegment);
                    
                    orbitLine.setLatLngs(pathSegments);

                    if (isFirstLoad) {
                        map.panTo(newLatLng, { animate: true, duration: 1.5 });
                        isFirstLoad = false;
                    }
                }
            }

            updateLiveTelemetry();
            setInterval(updateLiveTelemetry, 1000); 
            
            // Perbarui ukuran peta setelah struktur flexbox selesai dimuat
            setTimeout(function() { map.invalidateSize(); }, 500);
            
            // Tambahan: Pastikan peta me-resize ulang jika ukuran jendela berubah
            window.addEventListener('resize', function() {
                map.invalidateSize();
            });
        });

        function recenterMap() {
            if (map && lastKnownPos[0] !== 0) {
                map.panTo(lastKnownPos, { animate: true, duration: 1 });
            }
        }
    </script>
@endpush