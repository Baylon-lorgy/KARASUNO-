<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WaterLevel;
use Carbon\Carbon;

class WaterLevelSeeder extends Seeder
{
    public function run()
    {
        // Create data for the last 7 days
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::now()->subDays($i);
            
            // Create 24 records for each day (one per hour)
            for ($hour = 0; $hour < 24; $hour++) {
                WaterLevel::create([
                    'water_level' => rand(40, 80),
                    'soil_moisture' => rand(30, 70),
                    'temperature' => rand(20, 30),
                    'humidity' => rand(50, 90),
                    'status' => rand(0, 1) ? 'success' : 'failed',
                    'sensor_id' => 'sensor_1',
                    'location' => 'Main Tank',
                    'created_at' => $date->copy()->setHour($hour)
                ]);
            }
        }
    }
} 