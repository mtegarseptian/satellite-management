@extends('layouts.admin')

@section('title', 'Global Live Tracking')
@section('page-title', 'Global Live Tracking')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Live Tracking</li>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #globalSatelliteMap { height: 65vh; width: 100%; z-index: 1; border-radius: 0 0 4px 4px; background: #0b101d; }
        .leaflet-container img { max-width: none !important; max-height: none !important; }
        .satellite-marker-wrapper {
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            transform: translate(-50%, -10px); 
        }
        
        /* Satelit tetap menonjol (14px) */
        .blinking-dot {
            width: 14px; 
            height: 14px; 
            border-radius: 50%; border: 2px solid #ffffff;
            animation: pulse-generic 1.5s infinite;
        }
        
        .satellite-label {
            margin-top: 6px; 
            background-color: rgba(255, 255, 255, 0.9);
            padding: 2px 8px; 
            border-radius: 4px; 
            font-size: 11px; 
            font-weight: bold; color: #333; border: 1px solid #ccc;
            white-space: nowrap; box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        @keyframes pulse-generic {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 50px rgba(255, 255, 255, 0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
        }
        
        /* Ikon Ground Station diperkecil lagi menjadi 14px */
        .gs-custom-icon {
            display: flex; justify-content: center; align-items: center;
            background-color: rgba(255,255,255,0.9);
            border-radius: 50%; border: 2px solid;
            box-shadow: 0 0 3px rgba(0,0,0,0.4);
            width: 14px; 
            height: 14px; 
        }

        .dropdown-menu-filter { padding: 15px; } 
        #satellite-checkboxes { 
            max-height: 150px; overflow-y: auto; overflow-x: hidden; padding-right: 5px; 
        }
        #satellite-checkboxes::-webkit-scrollbar { width: 6px; }
        #satellite-checkboxes::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        #satellite-checkboxes::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
        #satellite-checkboxes::-webkit-scrollbar-thumb:hover { background: #555; }
        .sat-card-clickable { transition: transform 0.2s, box-shadow 0.2s; cursor: pointer; }
        .sat-card-clickable:hover { transform: translateY(-4px); box-shadow: 0 6px 12px rgba(0,0,0,0.15) !important; }
    </style>
@endpush

@section('content')
    <div class="row" id="stat-cards-container">
        @foreach($satellites as $sat)
        <div class="col-md-4 col-sm-6 stat-card-wrapper" id="wrapper-{{ $sat->id }}">
            <div class="card shadow-sm border-0 mb-3 sat-card-clickable" id="card-{{ $sat->id }}" style="border-top: 4px solid #333;" onclick="focusSatellite({{ $sat->id }})" title="Klik untuk mencari satelit di peta">
                <div class="card-body p-3">
                    <h6 class="font-weight-bold mb-1"><i class="fas fa-satellite mr-1"></i> {{ $sat->name }}</h6>
                    
                    <div class="text-muted small mb-2" style="font-size: 0.8rem;">
                        <i class="fas fa-broadcast-tower mr-1"></i> GS: 
                        <strong class="text-dark">{{ $sat->groundStation ? $sat->groundStation->name : 'Belum Terhubung' }}</strong>
                    </div>
                    
                    <div class="row text-sm mt-1">
                        <div class="col-6 mb-1"><span class="text-muted">Lat:</span> <strong id="lat-{{ $sat->id }}">--.----</strong></div>
                        <div class="col-6 mb-1"><span class="text-muted">Lng:</span> <strong id="lng-{{ $sat->id }}">--.----</strong></div>
                        <div class="col-6 mb-1"><span class="text-muted">Alt:</span> <strong class="text-success" id="alt-{{ $sat->id }}">--</strong> <small>km</small></div>
                        <div class="col-6 mb-1"><span class="text-muted">Spd:</span> <strong class="text-primary" id="vel-{{ $sat->id }}">--.---</strong> <small>km/s</small></div>
                        
                        <div class="col-12 my-1 border-top pt-2"></div>
                        <div class="col-4 mb-1"><span class="text-muted" title="Azimuth">Azimuth:</span> <strong id="azi-{{ $sat->id }}">--</strong>&deg;</div>
                        <div class="col-4 mb-1"><span class="text-muted" title="Elevation">Elevation:</span> <strong id="ele-{{ $sat->id }}">--</strong>&deg;</div>
                        <div class="col-4 mb-1"><span class="text-muted" title="Range / Jarak">Range:</span> <strong class="text-info" id="rng-{{ $sat->id }}">--</strong> <small>km</small></div>

                        <div class="col-12 mt-1 pt-2 border-top">
                            <span class="text-muted"><i class="far fa-clock mr-1"></i>Epoch:</span> 
                            <span id="epoch-{{ $sat->id }}" class="small font-weight-bold text-dark ml-1">Loading...</span>
                        </div>
                        
                        <div class="col-12 mt-1">
                            <span class="text-muted"><i class="fas fa-calendar-alt mr-1"></i>Prediksi AOS:</span> 
                            <span id="next-pass-{{ $sat->id }}" class="small font-weight-bold text-primary ml-1">Menghitung prediksi...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-dark d-flex align-items-center">
            <h3 class="card-title m-0"><i class="fas fa-map-marked-alt text-warning mr-2"></i> Global Tracking</h3>
            
            <div class="ml-auto d-flex">
                <div class="dropdown mr-2">
                    <button class="btn btn-sm btn-outline-warning dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <div class="dropdown-menu dropdown-menu-right p-3 dropdown-menu-filter" aria-labelledby="filterDropdown" style="width: 280px;" onclick="event.stopPropagation()">
                        <div class="custom-control custom-checkbox mb-3 pb-2 border-bottom">
                            <input class="custom-control-input" type="checkbox" id="checkAll" checked>
                            <label for="checkAll" class="custom-control-label font-weight-bold">Tampilkan Semua</label>
                        </div>
                        <div id="satellite-checkboxes">
                            @foreach($satellites as $sat)
                            <div class="custom-control custom-switch mb-2">
                                <input type="checkbox" class="custom-control-input sat-checkbox" id="chk-{{ $sat->id }}" value="{{ $sat->id }}" checked>
                                <label class="custom-control-label" style="cursor: pointer;" for="chk-{{ $sat->id }}">
                                    {{ $sat->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <button class="btn btn-sm btn-outline-light" onclick="resetMapView()">
                    <i class="fas fa-sync-alt"></i> Reset View
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="globalSatelliteMap"></div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/satellite.js/4.0.0/satellite.min.js"></script>

    <script>
        let map; 
        let trackers = {}; 

        document.addEventListener('DOMContentLoaded', function () {
            
            var bounds = [[-90, -Infinity], [90, Infinity]]; 
            map = L.map('globalSatelliteMap', {
                minZoom: 1, maxBounds: bounds, maxBoundsViscosity: 1.0,
                worldCopyJump: true, center: [0, 0], zoom: 2, zoomSnap: 0.5, zoomDelta: 0.5
            });

            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri'
            }).addTo(map);

            L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}')
            .addTo(map);

            const satellites = @json($satellites);
            const colors = ['#ff3333', '#00ff00', '#3366ff', '#ffff00', '#ff00ff', '#00ffff'];

            function parseTLEEpoch(tleLine1) {
                try {
                    let yearPart = tleLine1.substring(18, 20);
                    let dayPart = tleLine1.substring(20, 32);
                    let year = parseInt(yearPart, 10);
                    let days = parseFloat(dayPart);
                    year = (year < 57) ? year + 2000 : year + 1900;
                    let date = new Date(Date.UTC(year, 0, 1)); 
                    date.setUTCMilliseconds((days - 1) * 24 * 60 * 60 * 1000);
                    return date.toISOString().replace('T', ' ').substring(0, 23) + ' UTC';
                } catch (e) {
                    return "Invalid TLE Data";
                }
            }

            let gsSatellitesMap = {};
            satellites.forEach(s => {
                if (s.ground_station) {
                    let gsId = s.ground_station.id;
                    if (!gsSatellitesMap[gsId]) gsSatellitesMap[gsId] = [];
                    gsSatellitesMap[gsId].push(s.name);
                }
            });

            satellites.forEach((sat, index) => {
                let color = colors[index % colors.length];
                document.getElementById('card-' + sat.id).style.borderTopColor = color;
                document.getElementById(`epoch-${sat.id}`).innerText = parseTLEEpoch(sat.tle_line1);

                let customIcon = L.divIcon({
                    className: 'custom-icon',
                    html: `
                        <div class="satellite-marker-wrapper">
                            <div class="blinking-dot" style="background-color: ${color}; box-shadow: 0 0 0 rgba(${hexToRgb(color)}, 0.7);"></div>
                            <div class="satellite-label" style="border-bottom: 2px solid ${color}">${sat.name}</div>
                        </div>
                    `,
                    iconSize: [0, 0], iconAnchor: [0, 0]
                });

                let satPopupContent = `
                    <div style="min-width: 160px; font-family: Arial, sans-serif;">
                        <h6 class="mb-2 pb-2 border-bottom text-dark font-weight-bold" style="font-size: 14px;">
                            <i class="fas fa-satellite" style="color: ${color};"></i> ${sat.name}
                        </h6>
                        <div class="text-dark" style="font-size: 12px; line-height: 1.5;">
                            <span class="text-muted">Stasiun Pengendali (GS):</span><br>
                            <span class="font-weight-bold text-primary">${sat.ground_station ? sat.ground_station.name : 'Belum Terhubung'}</span>
                        </div>
                    </div>
                `;

                trackers[sat.id] = {
                    // SETTING LAYER: zIndexOffset: 1000 agar satelit selalu di paling atas
                    marker: L.marker([0, 0], {icon: customIcon, zIndexOffset: 1000}).bindPopup(satPopupContent).addTo(map), 
                    line: L.polyline([], {color: color, weight: 2, opacity: 0.6}).addTo(map),
                    footprint: L.circle([0, 0], {
                        color: color, weight: 1, fillColor: '#ffffff', fillOpacity: 0.15, dashArray: '5, 5'
                    }).addTo(map),
                    satrec: satellite.twoline2satrec(sat.tle_line1, sat.tle_line2)
                };

                if (sat.ground_station) {
                    let gsIcon = L.divIcon({
                        className: 'custom-icon',
                        html: `
                            <div class="satellite-marker-wrapper">
                                <div class="gs-custom-icon" style="border-color: ${color};">
                                    <i class="fas fa-satellite-dish" style="color: ${color}; font-size: 8px;"></i>
                                </div>
                                <div class="satellite-label" style="border-bottom: 2px solid ${color}; font-size: 8px;">
                                    GS: ${sat.ground_station.name}
                                </div>
                            </div>
                        `,
                        iconSize: [0, 0],
                        iconAnchor: [0, 0]
                    });
                    
                    let trackedSatsList = gsSatellitesMap[sat.ground_station.id]
                        .map(name => `<i class="fas fa-check-circle text-success mr-1"></i> ${name}`)
                        .join('<br>');

                    let gsPopupContent = `
                        <div style="min-width: 170px; font-family: Arial, sans-serif;">
                            <h6 class="mb-2 pb-2 border-bottom text-dark font-weight-bold" style="font-size: 13px;">
                                <i class="fas fa-broadcast-tower text-secondary"></i> GS: ${sat.ground_station.name}
                            </h6>
                            <div class="text-dark" style="font-size: 12px; line-height: 1.6;">
                                <span class="text-muted border-bottom d-inline-block mb-1">Daftar Satelit Terlacak:</span><br>
                                <span class="font-weight-bold">${trackedSatsList}</span>
                            </div>
                        </div>
                    `;

                    // SETTING LAYER: zIndexOffset: -1000 agar GS selalu di lapisan bawah
                    trackers[sat.id].gsMarker = L.marker([sat.ground_station.latitude, sat.ground_station.longitude], {
                        icon: gsIcon, 
                        title: sat.ground_station.name,
                        zIndexOffset: -1000
                    }).bindPopup(gsPopupContent).addTo(map);

                    trackers[sat.id].losLine = L.polyline([], {
                        color: color, weight: 1.5, dashArray: '5, 5', opacity: 0.8
                    }).addTo(map);
                }
            });

            function toggleSatellite(id, isVisible) {
                if (isVisible) {
                    trackers[id].marker.addTo(map);
                    trackers[id].line.addTo(map);
                    trackers[id].footprint.addTo(map);
                    if(trackers[id].gsMarker) trackers[id].gsMarker.addTo(map);
                    if(trackers[id].losLine) trackers[id].losLine.addTo(map);
                    document.getElementById('wrapper-' + id).style.display = 'block';
                } else {
                    map.removeLayer(trackers[id].marker);
                    map.removeLayer(trackers[id].line);
                    map.removeLayer(trackers[id].footprint);
                    if(trackers[id].gsMarker) map.removeLayer(trackers[id].gsMarker);
                    if(trackers[id].losLine) map.removeLayer(trackers[id].losLine);
                    document.getElementById('wrapper-' + id).style.display = 'none';
                }
            }

            document.getElementById('checkAll').addEventListener('change', function(e) {
                let isChecked = e.target.checked;
                document.querySelectorAll('.sat-checkbox').forEach(cb => {
                    cb.checked = isChecked;
                    toggleSatellite(cb.value, isChecked);
                });
            });

            document.querySelectorAll('.sat-checkbox').forEach(cb => {
                cb.addEventListener('change', function(e) {
                    toggleSatellite(e.target.value, e.target.checked);
                    let total = document.querySelectorAll('.sat-checkbox').length;
                    let checked = document.querySelectorAll('.sat-checkbox:checked').length;
                    document.getElementById('checkAll').checked = (total === checked);
                });
            });

            function updatePositions() {
                const now = new Date();

                satellites.forEach(sat => {
                    if (!document.getElementById('chk-' + sat.id).checked) return; 

                    const satrec = trackers[sat.id].satrec;
                    const positionAndVelocity = satellite.propagate(satrec, now);
                    const gmst = satellite.gstime(now);
                    
                    if (positionAndVelocity.position && positionAndVelocity.velocity) {
                        const positionGd = satellite.eciToGeodetic(positionAndVelocity.position, gmst);
                        const lat = satellite.degreesLat(positionGd.latitude);
                        const lng = satellite.degreesLong(positionGd.longitude);
                        const alt = positionGd.height;

                        const v = positionAndVelocity.velocity;
                        const speed = Math.sqrt(Math.pow(v.x, 2) + Math.pow(v.y, 2) + Math.pow(v.z, 2));

                        document.getElementById(`lat-${sat.id}`).innerText = lat.toFixed(3) + '°';
                        document.getElementById(`lng-${sat.id}`).innerText = lng.toFixed(3) + '°';
                        document.getElementById(`alt-${sat.id}`).innerText = alt.toFixed(0);
                        document.getElementById(`vel-${sat.id}`).innerText = speed.toFixed(3);

                        if (sat.ground_station) {
                            const observerGd = {
                                longitude: satellite.degreesToRadians(sat.ground_station.longitude),
                                latitude: satellite.degreesToRadians(sat.ground_station.latitude),
                                height: sat.ground_station.altitude / 1000 
                            };
                            
                            const positionEcf = satellite.eciToEcf(positionAndVelocity.position, gmst);
                            const lookAngles = satellite.ecfToLookAngles(observerGd, positionEcf);

                            const azimuth = satellite.radiansToDegrees(lookAngles.azimuth);
                            const elevation = satellite.radiansToDegrees(lookAngles.elevation);
                            const rangeSat = lookAngles.rangeSat;

                            document.getElementById(`azi-${sat.id}`).innerText = azimuth.toFixed(1);
                            document.getElementById(`ele-${sat.id}`).innerText = elevation.toFixed(1);
                            document.getElementById(`rng-${sat.id}`).innerText = rangeSat.toFixed(0);

                            const eleElement = document.getElementById(`ele-${sat.id}`);
                            if (elevation < 0) {
                                eleElement.classList.add('text-danger');
                                eleElement.classList.remove('text-success');
                                
                                trackers[sat.id].losLine.setLatLngs([]);
                            } else {
                                eleElement.classList.add('text-success');
                                eleElement.classList.remove('text-danger');
                                
                                trackers[sat.id].losLine.setLatLngs([
                                    [sat.ground_station.latitude, sat.ground_station.longitude],
                                    [lat, lng]
                                ]);
                            }
                        } else {
                            document.getElementById(`azi-${sat.id}`).innerText = "N/A";
                            document.getElementById(`ele-${sat.id}`).innerText = "N/A";
                            document.getElementById(`rng-${sat.id}`).innerText = "N/A";
                        }

                        trackers[sat.id].marker.setLatLng([lat, lng]);

                        const earthRadius = 6371; 
                        let footprintRadiusKm = Math.acos(earthRadius / (earthRadius + alt)) * earthRadius;
                        trackers[sat.id].footprint.setLatLng([lat, lng]);
                        trackers[sat.id].footprint.setRadius(footprintRadiusKm * 1000);

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

                                if (lastLng !== null && Math.abs(pLng - lastLng) > 90) {
                                    pathSegments.push(currentSegment);
                                    currentSegment = [];
                                }

                                currentSegment.push([pLat, pLng]);
                                lastLng = pLng;
                            }
                        }
                        if (currentSegment.length > 0) pathSegments.push(currentSegment);
                        trackers[sat.id].line.setLatLngs(pathSegments);
                    }
                });
            }

            function calculateNextPasses() {
                const now = new Date();
                
                satellites.forEach(sat => {
                    if (!sat.ground_station) {
                        document.getElementById(`next-pass-${sat.id}`).innerText = "N/A (Pilih Ground Station)";
                        document.getElementById(`next-pass-${sat.id}`).className = "small font-weight-bold text-muted ml-1";
                        return;
                    }

                    const satrec = trackers[sat.id].satrec;
                    const observerGd = {
                        longitude: satellite.degreesToRadians(sat.ground_station.longitude),
                        latitude: satellite.degreesToRadians(sat.ground_station.latitude),
                        height: sat.ground_station.altitude / 1000 
                    };

                    let nextPassTime = null;
                    let isCurrentlyPassing = false;

                    const currentPos = satellite.propagate(satrec, now);
                    if (currentPos.position) {
                        const currentEcf = satellite.eciToEcf(currentPos.position, satellite.gstime(now));
                        const currentLook = satellite.ecfToLookAngles(observerGd, currentEcf);
                        if (currentLook.elevation > 0) {
                            isCurrentlyPassing = true;
                        }
                    }

                    if (isCurrentlyPassing) {
                         document.getElementById(`next-pass-${sat.id}`).innerText = "Sinyal Terakuisisi (AOS)!";
                         document.getElementById(`next-pass-${sat.id}`).className = "small font-weight-bold text-success ml-1";
                         return;
                    }

                    for (let i = 1; i <= 1440; i++) {
                        let futureTime = new Date(now.getTime() + i * 60000);
                        let futurePos = satellite.propagate(satrec, futureTime);
                        
                        if (futurePos.position) {
                            let futureEcf = satellite.eciToEcf(futurePos.position, satellite.gstime(futureTime));
                            let futureLook = satellite.ecfToLookAngles(observerGd, futureEcf);
                            
                            if (futureLook.elevation > 0) {
                                nextPassTime = futureTime;
                                break;
                            }
                        }
                    }

                    if (nextPassTime) {
                        const timeString = nextPassTime.toLocaleString('id-ID', { 
                            day: '2-digit', month: 'short', hour: '2-digit', minute: '2-digit' 
                        });
                        document.getElementById(`next-pass-${sat.id}`).innerText = timeString + " WIB";
                    } else {
                        document.getElementById(`next-pass-${sat.id}`).innerText = "Tidak melintas 24 jam ke depan";
                        document.getElementById(`next-pass-${sat.id}`).className = "small font-weight-bold text-danger ml-1";
                    }
                });
            }

            calculateNextPasses();

            updatePositions();
            setInterval(updatePositions, 3000); 
            setTimeout(() => map.invalidateSize(), 500);
        });

        function resetMapView() {
            if(map) { map.setView([0, 0], 1); }
        }

        function focusSatellite(id) {
            if (map && trackers[id]) {
                let pos = trackers[id].marker.getLatLng();
                if (pos.lat !== 0 && pos.lng !== 0) {
                    map.flyTo(pos, 4, {
                        animate: true,
                        duration: 1.5
                    });
                }
            }
        }

        function hexToRgb(hex) {
            var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
            return result ? parseInt(result[1], 16) + ',' + parseInt(result[2], 16) + ',' + parseInt(result[3], 16) : '255,255,255';
        }
    </script>
@endpush