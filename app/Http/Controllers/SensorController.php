<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Services\SensorNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\SensorReading;
use Illuminate\Support\Facades\Http;
use App\Models\WateringThreshold;

class SensorController extends Controller
{
    public function update(Request $request)
    {
        try {
            $sensor = Sensor::firstOrCreate(['id' => 1]);
            $previousLevel = $sensor->water_level;
            $previousAvailability = $sensor->water_available;

            $sensor->update([
                'water_level' => $request->water_level,
                'water_available' => $request->water_available,
                'last_reading' => now()
            ]);

            // Check for water level changes
            if ($previousLevel != $request->water_level) {
                SensorNotificationService::notifyWaterLevelChange(
                    auth()->id(),
                    $request->water_level,
                    $previousLevel
                );
            }

            // Check for water availability changes
            if ($previousAvailability != $request->water_available) {
                SensorNotificationService::notifyWaterAvailability(
                    auth()->id(),
                    $request->water_available
                );
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Sensor update error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function wateringStatus(Request $request)
    {
        try {
            $status = $request->status;
            $duration = $request->duration;

            SensorNotificationService::notifyWateringStatus(
                auth()->id(),
                $status,
                $duration
            );

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Watering status error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getSensorData()
    {
        try {
            // Get the latest sensor reading
            $latestReading = SensorReading::latest()->first();

            if (!$latestReading) {
                return response()->json([
                    'soil_moisture' => 0,
                    'temperature' => 0,
                    'humidity' => 0,
                    'thresholds' => [
                        'soil_moisture' => 30,
                        'temperature' => 30,
                        'humidity' => 40
                    ]
                ]);
            }

            // Get the current thresholds
            $thresholds = WateringThreshold::first();

            return response()->json([
                'soil_moisture' => (float)$latestReading->soil_moisture,
                'temperature' => (float)$latestReading->temperature,
                'humidity' => (float)$latestReading->humidity,
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
            Log::error('Error fetching sensor data: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch sensor data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function storeSensorData(Request $request)
    {
        try {
            $validated = $request->validate([
                'soil_moisture' => 'required|numeric|between:0,100',
                'temperature' => 'required|numeric|between:-10,50',
                'humidity' => 'required|numeric|between:0,100'
            ]);

            $reading = SensorReading::create($validated);

            // Check if automatic watering is needed
            $this->checkAutomaticWatering($reading);

            return response()->json([
                'message' => 'Sensor data stored successfully',
                'data' => $reading
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing sensor data: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to store sensor data'
            ], 500);
        }
    }

    public function updateThresholds(Request $request)
    {
        try {
            $validated = $request->validate([
                'soil_moisture' => 'required|numeric|between:0,100',
                'temperature' => 'required|numeric|between:0,50',
                'humidity' => 'required|numeric|between:0,100'
            ]);

            // Get or create the first threshold record
            $thresholds = WateringThreshold::firstOrCreate(
                ['id' => 1], // Use ID 1 as the default record
                [
                    'soil_moisture' => 30,
                    'temperature' => 30,
                    'humidity' => 40
                ]
            );
            
            $thresholds->update($validated);

            return response()->json([
                'message' => 'Thresholds updated successfully',
                'data' => $thresholds
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating thresholds: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update thresholds',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    private function checkAutomaticWatering($reading)
    {
        // Get the latest watering status
        $latestWatering = \App\Models\WateringStatus::latest()->first();

        // If watering is already active, don't start again
        if ($latestWatering && !$latestWatering->should_water) {
            return;
        }

        // Get the thresholds from the database
        $thresholds = WateringThreshold::first();

        // Check if any threshold is met
        $needsWatering = $reading->soil_moisture < $thresholds->soil_moisture ||
                        $reading->temperature > $thresholds->temperature ||
                        $reading->humidity < $thresholds->humidity;

        if ($needsWatering) {
            // Start watering for 3 minutes
            \App\Models\WateringStatus::create([
                'should_water' => false,
                'duration' => 3,
                'remaining_time' => 180 // 3 minutes in seconds
            ]);

            // Log the automatic watering event
            Log::info('Automatic watering started due to sensor readings', [
                'soil_moisture' => $reading->soil_moisture,
                'temperature' => $reading->temperature,
                'humidity' => $reading->humidity,
                'thresholds' => $thresholds
            ]);
        }
    }
} 