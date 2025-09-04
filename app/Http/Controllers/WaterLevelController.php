<?php

namespace App\Http\Controllers;

use App\Models\WaterLevel;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WaterLevelController extends Controller
{
    public function index()
    {
        $waterLevels = WaterLevel::orderBy('created_at', 'desc')->get();
        
        if (request()->wantsJson()) {
            return response()->json($waterLevels);
        }

        return Inertia::render('WaterLevels/Index', [
            'waterLevels' => $waterLevels
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'water_level' => 'required|numeric|min:0|max:100',
            'soil_moisture' => 'required|numeric|min:0|max:100',
            'temperature' => 'required|numeric|min:-50|max:100',
            'humidity' => 'required|numeric|min:0|max:100',
            'status' => 'required|string|in:success,failed',
            'sensor_id' => 'required|string',
            'location' => 'required|string',
        ]);

        $waterLevel = WaterLevel::create($validated);

        return response()->json($waterLevel, 201);
    }
} 