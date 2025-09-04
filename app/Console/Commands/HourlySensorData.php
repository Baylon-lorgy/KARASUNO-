<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SensorReading;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HourlySensorData extends Command
{
    protected $signature = 'sensor:hourly';
    protected $description = 'Collect and store sensor data every hour (Philippines Time)';

    public function handle()
    {
        try {
            // Set timezone to Philippines
            $currentTime = Carbon::now('Asia/Manila');
            
            // Get latest sensor data
            $response = Http::get('http://192.168.1.6:8000/api/sensor-data/latest');
            
            if ($response->successful()) {
                $data = $response->json();
                
                // Create new sensor reading with Philippines time
                $reading = new SensorReading([
                    'water_level' => $data['water_level'] ?? 0,
                    'soil_moisture' => $data['soil_moisture'] ?? 0,
                    'temperature' => $data['temperature'] ?? 0,
                    'humidity' => $data['humidity'] ?? 0,
                    'water_float_status' => $data['water_float_status'] ?? 'no_water',
                    'http_status' => 200,
                    'response' => json_encode($data),
                    'timestamp' => $currentTime
                ]);
                
                $reading->save();

                Log::info('Hourly sensor reading stored successfully (PHT)', [
                    'id' => $reading->_id,
                    'timestamp' => $reading->timestamp->format('Y-m-d H:i:s T')
                ]);

                $this->info('Hourly sensor data collected and stored successfully at ' . $currentTime->format('Y-m-d H:i:s T'));
            } else {
                Log::error('Failed to fetch sensor data (PHT)', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'time' => $currentTime->format('Y-m-d H:i:s T')
                ]);
                $this->error('Failed to fetch sensor data: ' . $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Error in hourly sensor data collection (PHT): ' . $e->getMessage(), [
                'time' => Carbon::now('Asia/Manila')->format('Y-m-d H:i:s T')
            ]);
            $this->error('Error collecting hourly sensor data: ' . $e->getMessage());
        }
    }
} 