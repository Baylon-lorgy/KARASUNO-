<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use MongoDB\Client;
use Carbon\Carbon;

class SaveSensorData extends Command
{
    protected $signature = 'sensor:save-data';
    protected $description = 'Save sensor data to MongoDB at scheduled times';

    public function handle()
    {
        try {
            // Get current time
            $currentTime = Carbon::now();
            
            // Get sensor data from your API or source
            $response = Http::get('http://localhost:8000/api/sensor-data');
            $sensorData = $response->json();

            // Connect to MongoDB
            $client = new Client(env('MONGODB_URI'));
            $collection = $client->selectDatabase(env('MONGODB_DATABASE'))->selectCollection('sensor_readings');

            // Prepare data for storage
            $data = [
                'timestamp' => $currentTime,
                'temperature' => $sensorData['temperature'] ?? null,
                'humidity' => $sensorData['humidity'] ?? null,
                'soil_moisture' => $sensorData['soil_moisture'] ?? null,
                'light_level' => $sensorData['light_level'] ?? null,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ];

            // Insert data into MongoDB
            $collection->insertOne($data);

            $this->info('Sensor data saved successfully at ' . $currentTime->format('Y-m-d H:i:s'));
        } catch (\Exception $e) {
            $this->error('Error saving sensor data: ' . $e->getMessage());
        }
    }
} 