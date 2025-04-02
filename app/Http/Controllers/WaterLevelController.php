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
            'level' => 'required|numeric',
            'sensor_id' => 'required|string',
            'location' => 'required|string',
        ]);

        $waterLevel = WaterLevel::create($validated);

        return response()->json($waterLevel, 201);
    }
} 