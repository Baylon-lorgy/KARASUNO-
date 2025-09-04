<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SensorPerDay;
use App\Models\SensorReading;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SaveDailySensorData extends Command
{
    protected $signature = 'sensor:save-daily';
    protected $description = 'Save sensor data to sensor_per_days collection (5 PM PHT)';

    public function handle()
    {
        try {
            $currentTime = Carbon::now('Asia/Manila');
            
            // Get latest sensor data
            $response = Http::get('http://192.168.1.6:8000/api/sensor-data/latest');
            
            if ($response->successful()) {
                $data = $response->json();
                $today = $currentTime->format('Y-m-d');
                
                // Get or create today's record
                $dailyData = SensorPerDay::where('date', $today)->first();
                
                if ($dailyData) {
                    // Update existing record
                    $dailyData->update([
                        'readings_count' => $dailyData->readings_count + 1,
                        'success_count' => $dailyData->success_count + 1,
                        'water_level' => $data['water_level'] ?? 0,
                        'soil_moisture' => $data['soil_moisture'] ?? 0,
                        'temperature' => $data['temperature'] ?? 0,
                        'humidity' => $data['humidity'] ?? 0,
                        'water_float_status' => $data['water_float_status'] ?? 'no_water',
                        'average_water_level' => ($dailyData->average_water_level * $dailyData->readings_count + ($data['water_level'] ?? 0)) / ($dailyData->readings_count + 1),
                        'average_soil_moisture' => ($dailyData->average_soil_moisture * $dailyData->readings_count + ($data['soil_moisture'] ?? 0)) / ($dailyData->readings_count + 1),
                        'average_temperature' => ($dailyData->average_temperature * $dailyData->readings_count + ($data['temperature'] ?? 0)) / ($dailyData->readings_count + 1),
                        'average_humidity' => ($dailyData->average_humidity * $dailyData->readings_count + ($data['humidity'] ?? 0)) / ($dailyData->readings_count + 1),
                        'min_water_level' => min($dailyData->min_water_level ?? PHP_FLOAT_MAX, $data['water_level'] ?? PHP_FLOAT_MAX),
                        'max_water_level' => max($dailyData->max_water_level ?? 0, $data['water_level'] ?? 0),
                        'min_soil_moisture' => min($dailyData->min_soil_moisture ?? PHP_FLOAT_MAX, $data['soil_moisture'] ?? PHP_FLOAT_MAX),
                        'max_soil_moisture' => max($dailyData->max_soil_moisture ?? 0, $data['soil_moisture'] ?? 0),
                        'min_temperature' => min($dailyData->min_temperature ?? PHP_FLOAT_MAX, $data['temperature'] ?? PHP_FLOAT_MAX),
                        'max_temperature' => max($dailyData->max_temperature ?? 0, $data['temperature'] ?? 0),
                        'min_humidity' => min($dailyData->min_humidity ?? PHP_FLOAT_MAX, $data['humidity'] ?? PHP_FLOAT_MAX),
                        'max_humidity' => max($dailyData->max_humidity ?? 0, $data['humidity'] ?? 0)
                    ]);
                } else {
                    // Create new record
                    SensorPerDay::create([
                        'date' => $today,
                        'readings_count' => 1,
                        'success_count' => 1,
                        'failed_count' => 0,
                        'water_level' => $data['water_level'] ?? 0,
                        'soil_moisture' => $data['soil_moisture'] ?? 0,
                        'temperature' => $data['temperature'] ?? 0,
                        'humidity' => $data['humidity'] ?? 0,
                        'water_float_status' => $data['water_float_status'] ?? 'no_water',
                        'average_water_level' => $data['water_level'] ?? 0,
                        'average_soil_moisture' => $data['soil_moisture'] ?? 0,
                        'average_temperature' => $data['temperature'] ?? 0,
                        'average_humidity' => $data['humidity'] ?? 0,
                        'min_water_level' => $data['water_level'] ?? 0,
                        'max_water_level' => $data['water_level'] ?? 0,
                        'min_soil_moisture' => $data['soil_moisture'] ?? 0,
                        'max_soil_moisture' => $data['soil_moisture'] ?? 0,
                        'min_temperature' => $data['temperature'] ?? 0,
                        'max_temperature' => $data['temperature'] ?? 0,
                        'min_humidity' => $data['humidity'] ?? 0,
                        'max_humidity' => $data['humidity'] ?? 0
                    ]);
                }

                Log::info('Daily sensor data saved successfully (PHT)', [
                    'time' => $currentTime->format('Y-m-d H:i:s T'),
                    'date' => $today
                ]);

                $this->info('Daily sensor data saved successfully at ' . $currentTime->format('Y-m-d H:i:s T'));
            } else {
                Log::error('Failed to fetch sensor data for daily save (PHT)', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'time' => $currentTime->format('Y-m-d H:i:s T')
                ]);
                $this->error('Failed to fetch sensor data: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Error in daily sensor data save (PHT): ' . $e->getMessage(), [
                'time' => Carbon::now('Asia/Manila')->format('Y-m-d H:i:s T')
            ]);
            $this->error('Error saving daily sensor data: ' . $e->getMessage());
        }
    }
} 