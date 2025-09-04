<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Broadcast;

class SensorDataController extends Controller
{
    public function update(Request $request)
    {
        try {
            $data = $request->validate([
            'water_level' => 'required|numeric|min:0|max:100',
            'soil_moisture' => 'required|numeric|min:0|max:100',
                'temperature' => 'required|numeric',
            'humidity' => 'required|numeric|min:0|max:100',
                'water_float_status' => 'required|string|in:water_detected,no_water'
        ]);

            // Store the latest data in cache
            Cache::put('latest_sensor_data', $data, now()->addMinutes(5));

            // Log the received data
            Log::info('Received sensor data:', $data);

            // Broadcast the data to websocket channel
            try {
                Broadcast::channel('sensor-updates', $data);
            } catch (\Exception $e) {
                Log::error('Broadcasting error: ' . $e->getMessage());
                // Continue execution even if broadcasting fails
            }

            return response()->json([
                'message' => 'Data received successfully',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            Log::error('Error processing sensor data: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function latest()
    {
        try {
            $data = Cache::get('latest_sensor_data', [
                'water_level' => 0,
                'soil_moisture' => 0,
                'temperature' => 0,
                'humidity' => 0,
                'water_float_status' => 'no_water'
            ]);
            
            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Error retrieving sensor data: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
} 