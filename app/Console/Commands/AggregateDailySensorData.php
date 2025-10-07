<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WaterLevel;
use App\Models\SensorPerDay;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AggregateDailySensorData extends Command
{
    protected $signature = 'sensors:aggregate-daily {date? : The date to aggregate (YYYY-MM-DD)}';
    protected $description = 'Aggregate sensor data for a specific day';

    public function handle()
    {
        try {
            // Get the date to process (default to yesterday)
            $date = $this->argument('date') 
                ? Carbon::createFromFormat('Y-m-d', $this->argument('date'))
                : Carbon::yesterday();

            $this->info("Aggregating sensor data for {$date->format('Y-m-d')}");

            // Get all readings for the specified date
            $readings = WaterLevel::whereDate('created_at', $date)->get();

            if ($readings->isEmpty()) {
                $this->warn("No readings found for {$date->format('Y-m-d')}");
                return;
            }

            // Calculate aggregates
            $aggregates = [
                'date' => $date->format('Y-m-d'),
                'average_water_level' => round($readings->avg('water_level'), 2),
                'average_soil_moisture' => round($readings->avg('soil_moisture'), 2),
                'average_temperature' => round($readings->avg('temperature'), 2),
                'average_humidity' => round($readings->avg('humidity'), 2),
                'average_light_level' => round($readings->avg('light_level'), 2),
                'min_water_level' => round($readings->min('water_level'), 2),
                'max_water_level' => round($readings->max('water_level'), 2),
                'min_soil_moisture' => round($readings->min('soil_moisture'), 2),
                'max_soil_moisture' => round($readings->max('soil_moisture'), 2),
                'min_temperature' => round($readings->min('temperature'), 2),
                'max_temperature' => round($readings->max('temperature'), 2),
                'min_humidity' => round($readings->min('humidity'), 2),
                'max_humidity' => round($readings->max('humidity'), 2),
                'min_light_level' => round($readings->min('light_level'), 2),
                'max_light_level' => round($readings->max('light_level'), 2),
                'readings_count' => $readings->count(),
                'success_count' => $readings->where('status', 'success')->count(),
                'failed_count' => $readings->where('status', 'failed')->count()
            ];

            // Update or create the daily record
            SensorPerDay::updateOrCreate(
                ['date' => $date->format('Y-m-d')],
                $aggregates
            );

            $this->info("Successfully aggregated sensor data for {$date->format('Y-m-d')}");
            $this->info("Total readings processed: {$readings->count()}");

            Log::info("Daily sensor data aggregated for {$date->format('Y-m-d')}", [
                'readings_count' => $readings->count(),
                'success_count' => $aggregates['success_count'],
                'failed_count' => $aggregates['failed_count']
            ]);

        } catch (\Exception $e) {
            Log::error("Error aggregating daily sensor data: " . $e->getMessage());
            $this->error("Error: " . $e->getMessage());
        }
    }
} 