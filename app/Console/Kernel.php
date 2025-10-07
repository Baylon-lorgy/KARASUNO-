<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        Commands\CollectSensorData::class,
        Commands\UpdateSensorPerDay::class,
        Commands\HourlySensorData::class,
        Commands\SaveDailySensorData::class,
        Commands\SaveSensorDailyHistory::class,
        Commands\TestWateringNotification::class,
        Commands\CleanNullNotificationIds::class,
        Commands\TestScheduleConflicts::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Set timezone to Philippines
        $schedule->timezone('Asia/Manila');

        // Collect sensor data every hour (PHT)
        $schedule->command('sensor:hourly')
                ->hourly()
                ->withoutOverlapping()
                ->appendOutputTo(storage_path('logs/hourly-sensor-data.log'));

        // Save to sensor_per_days at 5 PM (PHT)
        $schedule->command('sensor:save-daily')
                ->dailyAt('17:00')
                ->appendOutputTo(storage_path('logs/daily-sensor-data.log'));

        // Run daily aggregation at midnight (PHT)
        $schedule->command('sensor:aggregate')
                ->dailyAt('00:00')
                ->appendOutputTo(storage_path('logs/daily-aggregation.log'));

        // Save daily sensor history at 11:59 PM (PHT)
        $schedule->command('sensor:save-daily-history')
                ->dailyAt('23:59')
                ->appendOutputTo(storage_path('logs/daily-sensor-history.log'));

        // Check watering schedules every minute
        $schedule->command('watering:check-schedules')
            ->everyMinute()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/watering-schedules.log'));

        // Check sensor rules every minute
        $schedule->command('watering:check-sensor-rules')
            ->everyMinute()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/sensor-rules.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
} 