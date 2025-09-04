<?php

namespace App\Console\Commands;

use App\Models\WateringSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckWateringSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'watering:check-schedules';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and execute watering schedules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $now = now();
            $currentDay = (string)$now->dayOfWeek;  // Convert to string for MongoDB comparison
            $currentTime = $now->format('H:i');

            $this->info("Checking schedules for day {$currentDay} at time {$currentTime}");
            Log::info("Checking watering schedules", [
                'current_day' => $currentDay,
                'current_time' => $currentTime
            ]);

            // Get active schedules for current day and time using MongoDB's $in operator
            $schedules = WateringSchedule::where('is_active', true)
                ->where('start_time', $currentTime)
                ->where('days_of_week', 'all', [$currentDay])  // Using MongoDB's 'all' operator
                ->get();

            $this->info("Found " . $schedules->count() . " matching schedules");
            Log::info("Found schedules", ['count' => $schedules->count()]);

            if ($schedules->isEmpty()) {
                $this->info('No active schedules found for current time.');
                
                // Log all schedules for debugging
                $allSchedules = WateringSchedule::all();
                $this->info("Total schedules in database: " . $allSchedules->count());
                foreach ($allSchedules as $schedule) {
                    $this->info("Schedule: time={$schedule->start_time}, days=" . json_encode($schedule->days_of_week) . ", active=" . ($schedule->is_active ? 'yes' : 'no'));
                }
                return;
            }

            // Check if watering is already active
            if (Cache::get('watering_active', false)) {
                $this->info('Watering is already active. Skipping schedule execution.');
                return;
            }

            foreach ($schedules as $schedule) {
                $this->info("Executing schedule: {$schedule->start_time} for {$schedule->duration} minutes");

                // Start watering
                $response = Http::post('http://127.0.0.1:8000/api/watering-control', [
                    'should_water' => false,
                    'duration' => $schedule->duration
                ]);

                if ($response->successful()) {
                    $this->info("Successfully started watering for {$schedule->duration} minutes");
                    Log::info("Started watering from schedule: {$schedule->start_time} for {$schedule->duration} minutes");
                } else {
                    $this->error("Failed to start watering: " . $response->body());
                    Log::error("Failed to start watering from schedule: {$schedule->start_time}", [
                        'response' => $response->body()
                    ]);
                }
            }
        } catch (\Exception $e) {
            $this->error("Error checking schedules: " . $e->getMessage());
            Log::error("Error checking watering schedules: " . $e->getMessage());
        }
    }
}
