<?php

namespace App\Http\Controllers;

use App\Models\GroundStation;
use Illuminate\Http\Request;

class GroundStationController extends Controller
{
    public function index()
    {
        $groundStations = GroundStation::withCount('satellites')->latest()->paginate(10);
        return view('ground_stations.index', compact('groundStations'));
    }

    public function create()
    {
        return view('ground_stations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'altitude' => 'nullable|numeric',
            'description' => 'nullable|string'
        ]);

        GroundStation::create($validated);

        return redirect()->route('ground-stations.index')
            ->with('success', 'Ground Station created successfully!');
    }

    public function show(GroundStation $groundStation)
    {
        $groundStation->load('satellites');
        return view('ground_stations.show', compact('groundStation'));
    }

    public function edit(GroundStation $groundStation)
    {
        return view('ground_stations.edit', compact('groundStation'));
    }

    public function update(Request $request, GroundStation $groundStation)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'altitude' => 'nullable|numeric',
            'description' => 'nullable|string'
        ]);

        $groundStation->update($validated);

        return redirect()->route('ground-stations.index')
            ->with('success', 'Ground Station updated successfully!');
    }

    public function destroy(GroundStation $groundStation)
    {
        $groundStation->delete();

        return redirect()->route('ground-stations.index')
            ->with('success', 'Ground Station deleted successfully!');
    }
}