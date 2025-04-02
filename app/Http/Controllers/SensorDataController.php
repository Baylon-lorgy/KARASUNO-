<?php

namespace App\Http\Controllers;

use App\Events\SensorDataUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SensorDataController extends Controller
{
    public function update(Request $request)
    {
        $validated = $request->validate([
            'water_level' => 'required|numeric|min:0|max:100',
            'soil_moisture' => 'required|numeric|min:0|max:100',
        ]);

        // Log the incoming data
        Log::info('Received sensor data', $validated);

        try {
            // Broadcast the event
            event(new SensorDataUpdated(
                $validated['water_level'],
                $validated['soil_moisture']
            ));

            Log::info('Event broadcasted successfully');
            
            return response()->json(['message' => 'Sensor data updated successfully']);
        } catch (\Exception $e) {
            Log::error('Broadcasting failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to broadcast data'], 500);
        }
    }
} 