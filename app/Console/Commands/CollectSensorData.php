<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WaterLevel;
use App\Models\SensorPerDay;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class CollectSensorData extends Command
{
    protected $signature = 'sensors:collect';
    protected $description = 'Collect sensor data at scheduled times (8am, 12pm, 4pm)';

    public function handle()
    {
        $currentTime = Carbon::now();
        $currentHour = $currentTime->hour;

        // Temporarily comment out time check for testing
        // if (!in_array($currentHour, [8, 12, 16])) {
        //     $this->info('Not a scheduled collection time. Skipping...');
        //     return;
        // }

        try {
            // Get data from ESP32
            $response = Http::post(config('app.esp32_url') . '/api/sensor-data/update', [
                'water_level' => 0,
                'soil_moisture' => 0,
                'temperature' => 0,
                'humidity' => 0,
                'water_float_status' => 'no_water'
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Save to water_levels collection
                $waterLevel = WaterLevel::create([
                    'water_level' => $data['water_level'] ?? null,
                    'soil_moisture' => $data['soil_moisture'] ?? null,
                    'temperature' => $data['temperature'] ?? null,
                    'humidity' => $data['humidity'] ?? null,
                    'water_float_status' => $data['water_float_status'] ?? null,
                    'status' => 'success',
                    'created_at' => $currentTime
                ]);

                // Update or create daily summary
                $today = $currentTime->format('Y-m-d');
                $dailyData = SensorPerDay::where('date', $today)->first();

                if ($dailyData) {
                    // Update existing daily record
                    $dailyData->update([
                        'readings_count' => $dailyData->readings_count + 1,
                        'success_count' => $dailyData->success_count + 1,
                        'water_level' => $data['water_level'] ?? null,
                        'soil_moisture' => $data['soil_moisture'] ?? null,
                        'temperature' => $data['temperature'] ?? null,
                        'humidity' => $data['humidity'] ?? null,
                        'water_float_status' => $data['water_float_status'] ?? null,
                        'average_water_level' => ($dailyData->average_water_level * $dailyData->readings_count + ($data['water_level'] ?? 0)) / ($dailyData->readings_count + 1),
                        'average_soil_moisture' => ($dailyData->average_soil_moisture * $dailyData->readings_count + ($data['soil_moisture'] ?? 0)) / ($dailyData->readings_count + 1),
                        'average_temperature' => ($dailyData->average_temperature * $dailyData->readings_count + ($data['temperature'] ?? 0)) / ($dailyData->readings_count + 1),
                        'average_humidity' => ($dailyData->average_humidity * $dailyData->readings_count + ($data['humidity'] ?? 0)) / ($dailyData->readings_count + 1),
                        'min_water_level' => min($dailyData->min_water_level, $data['water_level'] ?? PHP_FLOAT_MAX),
                        'max_water_level' => max($dailyData->max_water_level, $data['water_level'] ?? 0),
                        'min_soil_moisture' => min($dailyData->min_soil_moisture, $data['soil_moisture'] ?? PHP_FLOAT_MAX),
                        'max_soil_moisture' => max($dailyData->max_soil_moisture, $data['soil_moisture'] ?? 0),
                        'min_temperature' => min($dailyData->min_temperature, $data['temperature'] ?? PHP_FLOAT_MAX),
                        'max_temperature' => max($dailyData->max_temperature, $data['temperature'] ?? 0),
                        'min_humidity' => min($dailyData->min_humidity, $data['humidity'] ?? PHP_FLOAT_MAX),
                        'max_humidity' => max($dailyData->max_humidity, $data['humidity'] ?? 0)
                    ]);
                } else {
                    // Create new daily record
                    SensorPerDay::create([
                        'date' => $today,
                        'readings_count' => 1,
                        'success_count' => 1,
                        'failed_count' => 0,
                        'water_level' => $data['water_level'] ?? null,
                        'soil_moisture' => $data['soil_moisture'] ?? null,
                        'temperature' => $data['temperature'] ?? null,
                        'humidity' => $data['humidity'] ?? null,
                        'water_float_status' => $data['water_float_status'] ?? null,
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

                $this->info('Sensor data collected successfully at ' . $currentTime->format('H:i'));
            } else {
                // Log failed attempt in water_levels
                WaterLevel::create([
                    'status' => 'failed',
                    'created_at' => $currentTime
                ]);

                // Update failed count in daily summary
                $today = $currentTime->format('Y-m-d');
                $dailyData = SensorPerDay::where('date', $today)->first();

                if ($dailyData) {
                    $dailyData->update([
                        'readings_count' => $dailyData->readings_count + 1,
                        'failed_count' => $dailyData->failed_count + 1
                    ]);
                } else {
                    SensorPerDay::create([
                        'date' => $today,
                        'readings_count' => 1,
                        'success_count' => 0,
                        'failed_count' => 1
                    ]);
                }
                
                $this->error('Failed to collect sensor data: ' . $response->status());
            }
        } catch (\Exception $e) {
            // Log failed attempt in water_levels
            WaterLevel::create([
                'status' => 'failed',
                'created_at' => $currentTime
            ]);

            // Update failed count in daily summary
            $today = $currentTime->format('Y-m-d');
            $dailyData = SensorPerDay::where('date', $today)->first();

            if ($dailyData) {
                $dailyData->update([
                    'readings_count' => $dailyData->readings_count + 1,
                    'failed_count' => $dailyData->failed_count + 1
                ]);
            } else {
                SensorPerDay::create([
                    'date' => $today,
                    'readings_count' => 1,
                    'success_count' => 0,
                    'failed_count' => 1
                ]);
            }
            
            $this->error('Error collecting sensor data: ' . $e->getMessage());
        }
    }
} 