<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Notifications\WateringScheduleNotification;

class TestWateringNotification extends Command
{
    protected $signature = 'test:watering-notification {type=success}';
    protected $description = 'Test the watering notification system';

    public function handle()
    {
        $user = User::first();
        $type = $this->argument('type');

        if (!$user) {
            $this->error('No users found in the database!');
            return 1;
        }

        $schedule = "Monday 10:00 AM";
        $duration = "2 mins";

        if ($type === 'success') {
            $user->notify(new WateringScheduleNotification(
                $schedule,
                'COMPLETED',
                $duration,
                "Automatic Plant watering successful."
            ));
            $this->info('Success notification sent!');
        } else {
            $user->notify(new WateringScheduleNotification(
                $schedule,
                'FAILED',
                $duration,
                "Automatic Plan Watering Failed."
            ));
            $this->info('Failure notification sent!');
        }

        return 0;
    }
} 