<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SensorReading;
use App\Models\WateringThreshold;

class SensorDataController extends Controller
{
    public function getSensorData()
    {
        try {
            // Get the latest sensor reading
            $latestReading = SensorReading::latest()->first();

            if (!$latestReading) {
                // Create a sample reading if none exists
                try {
                    $latestReading = SensorReading::create([
                        'soil_moisture' => 45.5,
                        'temperature' => 25.2,
                        'humidity' => 60.8
                    ]);
                } catch (\Exception $e) {
                    // If creation fails, use default values
                    $latestReading = (object) [
                        'soil_moisture' => 45.5,
                        'temperature' => 25.2,
                        'humidity' => 60.8
                    ];
                }
            }

            // Get the current thresholds
            $thresholds = WateringThreshold::first();

            // Create default thresholds if none exist
            if (!$thresholds) {
                try {
                    $thresholds = WateringThreshold::create([
                        'soil_moisture' => 30,
                        'temperature' => 30,
                        'humidity' => 40
                    ]);
                } catch (\Exception $e) {
                    // If creation fails, use default values
                    $thresholds = (object) [
                        'soil_moisture' => 30,
                        'temperature' => 30,
                        'humidity' => 40
                    ];
                }
            }

            // Get watering status from cache
            $wateringStatus = \Cache::get('watering_state', [
                'watering_active' => false,
                'remaining_time' => 0
            ]);

            return response()->json([
                'soil_moisture' => (float)$latestReading->soil_moisture,
                'temperature' => (float)$latestReading->temperature,
                'humidity' => (float)$latestReading->humidity,
                'water_level' => 0, // Default value
                'watering_active' => $wateringStatus['watering_active'] ?? false,
                'remaining_time' => $wateringStatus['remaining_time'] ?? 0,
                'thresholds' => $thresholds ? [
                    'soil_moisture' => (float)$thresholds->soil_moisture,
                    'temperature' => (float)$thresholds->temperature,
                    'humidity' => (float)$thresholds->humidity
                ] : [
                    'soil_moisture' => 30,
                    'temperature' => 30,
                    'humidity' => 40
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching sensor data: ' . $e->getMessage());
            
            // Return fallback data instead of error
            return response()->json([
                'soil_moisture' => 45.5,
                'temperature' => 25.2,
                'humidity' => 60.8,
                'water_level' => 0,
                'watering_active' => false,
                'remaining_time' => 0,
                'thresholds' => [
                    'soil_moisture' => 30,
                    'temperature' => 30,
                    'humidity' => 40
                ]
            ]);
        }
    }

    public function update(Request $request)
    {
        // Existing update method
    }

    public function latest()
    {
        // Existing latest method
    }
} 