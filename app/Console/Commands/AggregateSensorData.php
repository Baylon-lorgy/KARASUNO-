<?php

namespace App\Console\Commands;

use App\Models\SensorData;
use App\Models\SensorPerDay;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AggregateSensorData extends Command
{
    protected $signature = 'sensor:aggregate {date?}';
    protected $description = 'Aggregate sensor data for a specific date';

    public function handle()
    {
        try {
            $date = $this->argument('date') 
                ? Carbon::parse($this->argument('date')) 
                : Carbon::now()->subDay();

            $this->info("Aggregating data for date: {$date->toDateString()}");

            // Get all sensor IDs
            $sensorIds = SensorData::distinct('sensor_id')->pluck('sensor_id');

            foreach ($sensorIds as $sensorId) {
                $this->aggregateForSensor($sensorId, $date);
            }

            $this->info('Aggregation completed successfully');
        } catch (\Exception $e) {
            Log::error('Error aggregating sensor data: ' . $e->getMessage());
            $this->error('Error: ' . $e->getMessage());
        }
    }

    protected function aggregateForSensor($sensorId, $date)
    {
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        $readings = SensorData::where('sensor_id', $sensorId)
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->get();

        if ($readings->isEmpty()) {
            $this->warn("No readings found for sensor {$sensorId} on {$date->toDateString()}");
            return;
        }

        $stats = [
            'date' => $date->toDateString(),
            'sensor_id' => $sensorId,
            'average_water_level' => round($readings->avg('water_level'), 2),
            'average_soil_moisture' => round($readings->avg('soil_moisture'), 2),
            'average_temperature' => round($readings->avg('temperature'), 2),
            'average_humidity' => round($readings->avg('humidity'), 2),
            'min_water_level' => round($readings->min('water_level'), 2),
            'max_water_level' => round($readings->max('water_level'), 2),
            'min_soil_moisture' => round($readings->min('soil_moisture'), 2),
            'max_soil_moisture' => round($readings->max('soil_moisture'), 2),
            'min_temperature' => round($readings->min('temperature'), 2),
            'max_temperature' => round($readings->max('temperature'), 2),
            'min_humidity' => round($readings->min('humidity'), 2),
            'max_humidity' => round($readings->max('humidity'), 2),
            'readings_count' => $readings->count(),
            'success_count' => $readings->where('status', 'success')->count(),
            'failed_count' => $readings->where('status', 'failed')->count()
        ];

        SensorPerDay::updateOrCreate(
            ['date' => $date->toDateString(), 'sensor_id' => $sensorId],
            $stats
        );

        $this->info("Aggregated data for sensor {$sensorId} on {$date->toDateString()}");
    }
} 