<?php

namespace App\Console\Commands;

use App\Models\WateringSchedule;
use Illuminate\Console\Command;

class TestScheduleConflicts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:test-conflicts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test overlapping schedule validation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing overlapping schedule validation...');

        // Clear existing schedules for testing
        WateringSchedule::truncate();

        try {
            // Create first schedule
            $this->info('Creating first schedule: 08:00 for 3 minutes on Mon, Wed, Fri');
            $schedule1 = WateringSchedule::create([
                'start_time' => '08:00',
                'duration' => 3,
                'days_of_week' => [1, 3, 5], // Mon, Wed, Fri
                'is_active' => true
            ]);
            $this->info('✓ First schedule created successfully');

            // Try to create overlapping schedule (should fail)
            $this->info('Attempting to create overlapping schedule: 08:02 for 2 minutes on Mon, Wed, Fri');
            try {
                $schedule2 = WateringSchedule::create([
                    'start_time' => '08:02',
                    'duration' => 2,
                    'days_of_week' => [1, 3, 5], // Mon, Wed, Fri
                    'is_active' => true
                ]);
                $this->error('✗ Overlapping schedule was created (this should have failed)');
            } catch (\InvalidArgumentException $e) {
                $this->info('✓ Overlapping schedule correctly rejected: ' . $e->getMessage());
            }

            // Try to create non-overlapping schedule (should succeed)
            $this->info('Attempting to create non-overlapping schedule: 09:00 for 2 minutes on Mon, Wed, Fri');
            try {
                $schedule3 = WateringSchedule::create([
                    'start_time' => '09:00',
                    'duration' => 2,
                    'days_of_week' => [1, 3, 5], // Mon, Wed, Fri
                    'is_active' => true
                ]);
                $this->info('✓ Non-overlapping schedule created successfully');
            } catch (\InvalidArgumentException $e) {
                $this->error('✗ Non-overlapping schedule was rejected: ' . $e->getMessage());
            }

            // Try to create schedule on different days (should succeed)
            $this->info('Attempting to create schedule on different days: 08:30 for 2 minutes on Tue, Thu');
            try {
                $schedule4 = WateringSchedule::create([
                    'start_time' => '08:30',
                    'duration' => 2,
                    'days_of_week' => [2, 4], // Tue, Thu
                    'is_active' => true
                ]);
                $this->info('✓ Schedule on different days created successfully');
            } catch (\InvalidArgumentException $e) {
                $this->error('✗ Schedule on different days was rejected: ' . $e->getMessage());
            }

            // Test partial day overlap
            $this->info('Attempting to create schedule with partial day overlap: 08:30 for 2 minutes on Mon, Tue');
            try {
                $schedule5 = WateringSchedule::create([
                    'start_time' => '08:30',
                    'duration' => 2,
                    'days_of_week' => [1, 2], // Mon, Tue (Mon overlaps, Tue doesn't)
                    'is_active' => true
                ]);
                $this->error('✗ Schedule with partial day overlap was created (this should have failed)');
            } catch (\InvalidArgumentException $e) {
                $this->info('✓ Schedule with partial day overlap correctly rejected: ' . $e->getMessage());
            }

            // Display all schedules
            $this->info("\nAll schedules in database:");
            $schedules = WateringSchedule::all();
            foreach ($schedules as $schedule) {
                $days = collect($schedule->days_of_week)->map(function($day) {
                    $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    return $dayNames[(int)$day] ?? $day;
                })->implode(', ');
                $this->line("- {$schedule->start_time} ({$schedule->duration} min) on {$days}");
            }

            // Test conflict checking methods
            $this->info("\nTesting conflict checking methods:");
            $conflicts = WateringSchedule::getAllConflicts();
            if (empty($conflicts)) {
                $this->info('✓ No conflicts detected in existing schedules');
            } else {
                $this->warn('⚠ Conflicts detected in existing schedules:');
                foreach ($conflicts as $conflict) {
                    $this->warn('- Schedule conflicts found');
                }
            }

        } catch (\Exception $e) {
            $this->error('Test failed with error: ' . $e->getMessage());
            return 1;
        }

        $this->info("\n✓ All tests completed successfully!");
        return 0;
    }
} 