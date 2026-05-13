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

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by country
        if ($request->has('country') && $request->country != '') {
            $query->byCountry($request->country);
        }

        // Filter by orbit
        if ($request->has('orbit') && $request->orbit != '') {
            $query->byOrbit($request->orbit);
        }

        // Filter by status
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
            'norad_id' => 'nullable|string|max:50', // HARUS ADA INI
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

        return redirect()->route('satellites.index')
            ->with('success', 'Satellite created successfully!');
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

        return redirect()->route('satellites.index')
            ->with('success', 'Satellite updated successfully!');
    }

    public function destroy(Satellite $satellite)
    {
        if ($satellite->image) {
            Storage::disk('public')->delete($satellite->image);
        }

        $satellite->delete();

        return redirect()->route('satellites.index')
            ->with('success', 'Satellite deleted successfully!');
    }
    
    public function liveTracking()
    {
        // Mengambil semua satelit aktif yang TLE-nya terisi
        $satellites = Satellite::active()
            ->whereNotNull('tle_line1')
            ->whereNotNull('tle_line2')
            ->get();

        return view('satellites.live', compact('satellites'));
    }

    // Fungsi Baru: Menarik data TLE terbaru khusus untuk satu satelit
    public function syncSingleTLE(Satellite $satellite)
    {
        $url = '';
        $isGlobal = false;

        // Tentukan Strategi Pengambilan Data
        if (!empty($satellite->norad_id)) {
            // Prioritas 1: Jika ada NORAD ID, tembak ke CelesTrak (Global API)
            $url = "https://celestrak.org/NORAD/elements/gp.php?CATNR={$satellite->norad_id}&FORMAT=tle";
            $isGlobal = true;
        } else {
            // Prioritas 2: Jika kosong, gunakan IP Lokal BRIN
            $url = 'http://10.35.0.104/tle/LAPANSAT-TLE.txt';
        }

        try {
            $response = Http::timeout(15)->get($url);

            if ($response->successful()) {
                // Pecah teks berdasarkan baris baru
                $lines = array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $response->body())));
                $lines = array_values($lines);

                // Cek apakah API CelesTrak mengembalikan error string seperti "No TLE found"
                if (count($lines) > 0 && stripos($lines[0], 'No TLE found') !== false) {
                    return redirect()->back()->with('error', 'NORAD ID tidak ditemukan di database global CelesTrak.');
                }

                $isUpdated = false;

                // Looping 3 baris TLE
                for ($i = 0; $i < count($lines); $i += 3) {
                    if (isset($lines[$i + 2])) {
                        $nameFromTxt = trim($lines[$i]);
                        $tle1 = trim($lines[$i + 1]);
                        $tle2 = trim($lines[$i + 2]);

                        // Pencocokan: 
                        // Jika dari CelesTrak (isGlobal), asumsikan data sudah pasti milik ID tersebut
                        // Jika dari BRIN, cocokkan berdasarkan Nama
                        if ($isGlobal || stripos($nameFromTxt, $satellite->name) !== false || stripos($satellite->name, $nameFromTxt) !== false) {
                            
                            $satellite->update([
                                'tle_line1' => $tle1,
                                'tle_line2' => $tle2,
                            ]);
                            
                            $isUpdated = true;
                            break; // Stop pencarian setelah sukses
                        }
                    }
                }

                if ($isUpdated) {
                    $sumber = $isGlobal ? 'CelesTrak (Global)' : 'Server LAPAN (Lokal)';
                    return redirect()->back()->with('success', "Data TLE untuk {$satellite->name} berhasil diperbarui dari {$sumber}.");
                } else {
                    return redirect()->back()->with('warning', "Data ditemukan tetapi namanya tidak cocok dengan sistem.");
                }
            }

            return redirect()->back()->with('error', 'Gagal menghubungi server penyedia TLE.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Koneksi ke server gagal: ' . $e->getMessage());
        }
    }
}