<?php

namespace App\Console\Commands;

use App\Models\SensorPerDay;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateSensorPerDay extends Command
{
    protected $signature = 'sensor:update-per-day {data}';
    protected $description = 'Update sensor_per_days collection with new sensor data';

    public function handle()
    {
        try {
            $data = json_decode($this->argument('data'), true);
            
            if (!$data) {
                $this->error('Invalid JSON data provided');
                return;
            }

            $today = Carbon::now()->format('Y-m-d');
            
            // Get or create the daily record
            $dailyData = SensorPerDay::where('date', $today)->first();

            if ($dailyData) {
                // Update existing record
                $dailyData->update([
                    'readings_count' => $dailyData->readings_count + 1,
                    'success_count' => $dailyData->success_count + 1,
                    'water_level' => $data['water_level'],
                    'soil_moisture' => $data['soil_moisture'],
                    'temperature' => $data['temperature'],
                    'humidity' => $data['humidity'],
                    'water_float_status' => $data['water_float_status'],
                    'average_water_level' => ($dailyData->average_water_level * $dailyData->readings_count + $data['water_level']) / ($dailyData->readings_count + 1),
                    'average_soil_moisture' => ($dailyData->average_soil_moisture * $dailyData->readings_count + $data['soil_moisture']) / ($dailyData->readings_count + 1),
                    'average_temperature' => ($dailyData->average_temperature * $dailyData->readings_count + $data['temperature']) / ($dailyData->readings_count + 1),
                    'average_humidity' => ($dailyData->average_humidity * $dailyData->readings_count + $data['humidity']) / ($dailyData->readings_count + 1),
                    'min_water_level' => min($dailyData->min_water_level, $data['water_level']),
                    'max_water_level' => max($dailyData->max_water_level, $data['water_level']),
                    'min_soil_moisture' => min($dailyData->min_soil_moisture, $data['soil_moisture']),
                    'max_soil_moisture' => max($dailyData->max_soil_moisture, $data['soil_moisture']),
                    'min_temperature' => min($dailyData->min_temperature, $data['temperature']),
                    'max_temperature' => max($dailyData->max_temperature, $data['temperature']),
                    'min_humidity' => min($dailyData->min_humidity, $data['humidity']),
                    'max_humidity' => max($dailyData->max_humidity, $data['humidity'])
                ]);
            } else {
                // Create new record
                SensorPerDay::create([
                    'date' => $today,
                    'readings_count' => 1,
                    'success_count' => 1,
                    'failed_count' => 0,
                    'water_level' => $data['water_level'],
                    'soil_moisture' => $data['soil_moisture'],
                    'temperature' => $data['temperature'],
                    'humidity' => $data['humidity'],
                    'water_float_status' => $data['water_float_status'],
                    'average_water_level' => $data['water_level'],
                    'average_soil_moisture' => $data['soil_moisture'],
                    'average_temperature' => $data['temperature'],
                    'average_humidity' => $data['humidity'],
                    'min_water_level' => $data['water_level'],
                    'max_water_level' => $data['water_level'],
                    'min_soil_moisture' => $data['soil_moisture'],
                    'max_soil_moisture' => $data['soil_moisture'],
                    'min_temperature' => $data['temperature'],
                    'max_temperature' => $data['temperature'],
                    'min_humidity' => $data['humidity'],
                    'max_humidity' => $data['humidity']
                ]);
            }

            $this->info('Sensor data updated successfully in sensor_per_days collection');
        } catch (\Exception $e) {
            $this->error('Error updating sensor data: ' . $e->getMessage());
        }
    }
} 