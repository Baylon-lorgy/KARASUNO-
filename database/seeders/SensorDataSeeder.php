<?php

namespace Database\Seeders;

use App\Models\SensorData;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SensorDataSeeder extends Seeder
{
    public function run()
    {
        $sensorIds = ['sensor1', 'sensor2', 'sensor3'];
        $startDate = Carbon::now()->subDays(7);
        $endDate = Carbon::now();

        foreach ($sensorIds as $sensorId) {
            $currentDate = $startDate->copy();
            
            while ($currentDate <= $endDate) {
                // Generate readings for each hour
                for ($hour = 0; $hour < 24; $hour++) {
                    // Generate readings every 5 minutes
                    for ($minute = 0; $minute < 60; $minute += 5) {
                        $timestamp = $currentDate->copy()
                            ->setHour($hour)
                            ->setMinute($minute)
                            ->setSecond(0);

                        // Generate realistic sensor readings
                        $waterLevel = $this->generateRealisticWaterLevel($hour);
                        $soilMoisture = $this->generateRealisticSoilMoisture($hour);
                        $temperature = $this->generateRealisticTemperature($hour);
                        $humidity = $this->generateRealisticHumidity($hour);

                        SensorData::create([
                            'sensor_id' => $sensorId,
                            'water_level' => $waterLevel,
                            'soil_moisture' => $soilMoisture,
                            'temperature' => $temperature,
                            'humidity' => $humidity,
                            'status' => 'success',
                            'created_at' => $timestamp,
                            'updated_at' => $timestamp,
                        ]);
                    }
                }
                $currentDate->addDay();
            }
        }

        $this->command->info('Sensor data seeded successfully!');
    }

    private function generateRealisticWaterLevel($hour)
    {
        // Water level typically higher at night, lower during day
        $baseLevel = 50;
        $variation = sin(($hour - 6) * M_PI / 12) * 20; // 6am is peak
        return max(0, min(100, $baseLevel + $variation));
    }

    private function generateRealisticSoilMoisture($hour)
    {
        // Soil moisture decreases during day, increases at night
        $baseLevel = 60;
        $variation = sin(($hour - 12) * M_PI / 12) * 15; // Noon is lowest
        return max(0, min(100, $baseLevel + $variation));
    }

    private function generateRealisticTemperature($hour)
    {
        // Temperature follows daily cycle
        $baseTemp = 25;
        $variation = sin(($hour - 14) * M_PI / 12) * 5; // 2pm is hottest
        return max(15, min(35, $baseTemp + $variation));
    }

    private function generateRealisticHumidity($hour)
    {
        // Humidity typically higher at night, lower during day
        $baseLevel = 70;
        $variation = sin(($hour - 6) * M_PI / 12) * 15; // 6am is highest
        return max(30, min(100, $baseLevel + $variation));
    }
} 