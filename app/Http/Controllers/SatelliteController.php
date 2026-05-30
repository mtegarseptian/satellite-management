<?php

namespace App\Http\Controllers;

use App\Models\Satellite;
use App\Models\GroundStation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class SatelliteController extends Controller
{
    public function index(Request $request)
    {
        $query = Satellite::with('groundStation');

        if ($request->has('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->has('country') && $request->country != '') {
            $query->byCountry($request->country);
        }

        if ($request->has('orbit') && $request->orbit != '') {
            $query->byOrbit($request->orbit);
        }

        if ($request->has('status') && $request->status != '') {
            if ($request->status == 'active') {
                $query->active();
            } else {
                $query->inactive();
            }
        }

        $satellites = $query->latest()->paginate(10);
        $countries = Satellite::distinct()->pluck('country');
        
        return view('satellites.index', compact('satellites', 'countries'));
    }

    public function create()
    {
        $groundStations = GroundStation::all();
        return view('satellites.create', compact('groundStations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'norad_id' => 'nullable|string|max:50',
            'tle_url' => 'nullable|url|max:255', // <-- Tambahan validasi
            'country' => 'required|string|max:255',
            'launch_date' => 'required|date',
            'orbit_type' => 'required|in:LEO,MEO,GEO',
            'tle_line1' => 'nullable|string|size:69', 
            'tle_line2' => 'nullable|string|size:69', 
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
            'ground_station_id' => 'nullable|exists:ground_stations,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('satellites', 'public');
        }

        Satellite::create($validated);

        return redirect()->route('satellites.index')->with('success', 'Satellite created successfully!');
    }

    public function show(Satellite $satellite)
    {
        return view('satellites.show', compact('satellite'));
    }

    public function edit(Satellite $satellite)
    {
        $groundStations = GroundStation::all();
        return view('satellites.edit', compact('satellite', 'groundStations'));
    }

    public function update(Request $request, Satellite $satellite)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'norad_id' => 'nullable|string|max:50',
            'tle_url' => 'nullable|url|max:255',  
            'country' => 'required|string|max:255',
            'launch_date' => 'required|date',
            'orbit_type' => 'required|in:LEO,MEO,GEO',
            'tle_line1' => 'nullable|string|size:69', 
            'tle_line2' => 'nullable|string|size:69', 
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
            'ground_station_id' => 'nullable|exists:ground_stations,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('image')) {
            if ($satellite->image) {
                Storage::disk('public')->delete($satellite->image);
            }
            $validated['image'] = $request->file('image')->store('satellites', 'public');
        }

        $satellite->update($validated);

        return redirect()->route('satellites.index')->with('success', 'Satellite updated successfully!');
    }

    public function destroy(Satellite $satellite)
    {
        if ($satellite->image) {
            Storage::disk('public')->delete($satellite->image);
        }
        $satellite->delete();

        return redirect()->route('satellites.index')->with('success', 'Satellite deleted successfully!');
    }
    
    public function liveTracking()
    {
        $satellites = Satellite::active()
            ->whereNotNull('tle_line1')
            ->whereNotNull('tle_line2')
            ->get();

        return view('satellites.live', compact('satellites'));
    }

    // Fungsi Sinkronisasi TLE dengan URL Dinamis
    public function syncSingleTLE(Satellite $satellite)
    {
        $url = '';
        $sumber = '';
        $isGlobal = false; // Flag khusus CelesTrak karena format pencocokannya beda

        // STRATEGI PENGAMBILAN DATA (PRIORITAS: URL Dinamis > CelesTrak > Lokal)
        // STRATEGI PENGAMBILAN DATA
        if (!empty($satellite->tle_url)) {
            $url = $satellite->tle_url;
            $sumber = 'Custom API URL';
        } elseif (!empty($satellite->norad_id)) {
            $url = "https://celestrak.org/NORAD/elements/gp.php?CATNR={$satellite->norad_id}&FORMAT=tle";
            $sumber = 'CelesTrak (Global)';
            $isGlobal = true;
        } else {
            // PERUBAHAN LOGIKA: Cegah update otomatis jika tidak ada referensi link
            return redirect()->back()->with('error', 'Gagal memperbarui: Satelit ini belum memiliki Dynamic API URL atau NORAD ID.');
        }

        try {
            $response = Http::timeout(15)->get($url);

            if ($response->successful()) {
                $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $response->body())));
                $lines = array_values($lines);

                if (count($lines) > 0 && stripos($lines[0], 'No TLE found') !== false) {
                    return redirect()->back()->with('error', 'Satelit tidak ditemukan di database global CelesTrak.');
                }

                $isUpdated = false;

                // Looping setiap 3 baris (Nama, Line 1, Line 2)
                for ($i = 0; $i < count($lines); $i += 3) {
                    if (isset($lines[$i + 2])) {
                        $nameFromTxt = trim($lines[$i]);
                        $tle1 = trim($lines[$i + 1]);
                        $tle2 = trim($lines[$i + 2]);

                        // PERBAIKAN LOGIKA PENCOCOKAN: 
                        // Jika dari CelesTrak API ($isGlobal = true), data pasti hanya berisi 1 satelit, jadi langsung ambil.
                        // Namun jika dari URL Lokal/Custom API, sistem diwajibkan mencocokkan NAMANYA karena file bisa berisi puluhan satelit.
                        if ($isGlobal || stripos($nameFromTxt, $satellite->name) !== false || stripos($satellite->name, $nameFromTxt) !== false) {
                            
                            $satellite->update([
                                'tle_line1' => $tle1,
                                'tle_line2' => $tle2,
                            ]);
                            
                            $isUpdated = true;
                            break; // Hentikan pencarian jika sudah ketemu
                        }
                    }
                }

                if ($isUpdated) {
                    return redirect()->back()->with('success', "Data TLE untuk {$satellite->name} berhasil diperbarui dari {$sumber}.");
                } else {
                    return redirect()->back()->with('warning', "Data ditemukan di {$sumber}, tetapi namanya ('{$satellite->name}') tidak cocok dengan isi file teks.");
                }
            }

            return redirect()->back()->with('error', "Gagal menghubungi server penyedia TLE ({$sumber}).");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Koneksi ke {$sumber} gagal: " . $e->getMessage());
        }
    }
}