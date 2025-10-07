<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class WateringSchedule extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'watering_schedules';

    protected $fillable = [
        'start_time',
        'duration',
        'days_of_week',
        'is_active'
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'is_active' => 'boolean'
    ];

    protected static function booted()
    {
        static::creating(function ($schedule) {
            // Validate duration
            if ($schedule->duration < 1 || $schedule->duration > 5) {
                throw new \InvalidArgumentException('Duration must be between 1 and 5 minutes');
            }

            // Validate and convert days of week to strings
            if (empty($schedule->days_of_week)) {
                throw new \InvalidArgumentException('At least one day of the week must be selected');
            }

            // Convert days to strings for MongoDB
            $schedule->days_of_week = array_map('strval', $schedule->days_of_week);

            foreach ($schedule->days_of_week as $day) {
                if (!is_numeric($day) || $day < 0 || $day > 6) {
                    throw new \InvalidArgumentException('Invalid day of week value');
                }
            }

            // Format start_time to H:i format
            if (isset($schedule->start_time)) {
                $schedule->start_time = date('H:i', strtotime($schedule->start_time));
            }

            // Check for overlapping schedules
            $conflicts = self::checkForOverlappingSchedules($schedule);
            if (!empty($conflicts)) {
                $conflictDetails = collect($conflicts)->map(function($conflict) {
                    $days = collect($conflict->days_of_week)->map(function($day) {
                        $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                        return $dayNames[(int)$day] ?? $day;
                    })->implode(', ');
                    return "{$conflict->start_time} ({$days})";
                })->implode(', ');
                
                throw new \InvalidArgumentException("Schedule conflicts with existing schedules: {$conflictDetails}");
            }
        });

        static::updating(function ($schedule) {
            // Convert days to strings for MongoDB when updating
            if (isset($schedule->days_of_week)) {
                $schedule->days_of_week = array_map('strval', $schedule->days_of_week);
            }

            // Format start_time to H:i format when updating
            if (isset($schedule->start_time)) {
                $schedule->start_time = date('H:i', strtotime($schedule->start_time));
            }

            // Check for overlapping schedules (excluding current schedule)
            $conflicts = self::checkForOverlappingSchedules($schedule, $schedule->id);
            if (!empty($conflicts)) {
                $conflictDetails = collect($conflicts)->map(function($conflict) {
                    $days = collect($conflict->days_of_week)->map(function($day) {
                        $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                        return $dayNames[(int)$day] ?? $day;
                    })->implode(', ');
                    return "{$conflict->start_time} ({$days})";
                })->implode(', ');
                
                throw new \InvalidArgumentException("Schedule conflicts with existing schedules: {$conflictDetails}");
            }
        });
    }

    /**
     * Check for overlapping schedules
     * 
     * @param WateringSchedule $newSchedule
     * @param string|null $excludeId Schedule ID to exclude from check (for updates)
     * @return array Array of conflicting schedules
     */
    public static function checkForOverlappingSchedules($newSchedule, $excludeId = null)
    {
        $conflicts = [];
        
        // Get all active schedules (excluding the current one if updating)
        $query = self::where('is_active', true);
        if ($excludeId) {
            $query->where('_id', '!=', $excludeId);
        }
        $existingSchedules = $query->get();

        foreach ($existingSchedules as $existingSchedule) {
            // Check if schedules overlap on any day
            $overlappingDays = array_intersect($newSchedule->days_of_week, $existingSchedule->days_of_week);
            
            if (!empty($overlappingDays)) {
                // Check if time ranges overlap
                if (self::timeRangesOverlap(
                    $newSchedule->start_time, 
                    $newSchedule->duration,
                    $existingSchedule->start_time, 
                    $existingSchedule->duration
                )) {
                    $conflicts[] = $existingSchedule;
                }
            }
        }

        return $conflicts;
    }

    /**
     * Check if two time ranges overlap
     * 
     * @param string $startTime1 First schedule start time (H:i format)
     * @param int $duration1 First schedule duration in minutes
     * @param string $startTime2 Second schedule start time (H:i format)
     * @param int $duration2 Second schedule duration in minutes
     * @return bool True if schedules overlap
     */
    private static function timeRangesOverlap($startTime1, $duration1, $startTime2, $duration2)
    {
        // Convert times to minutes since midnight for easier comparison
        $start1 = self::timeToMinutes($startTime1);
        $end1 = $start1 + $duration1;
        $start2 = self::timeToMinutes($startTime2);
        $end2 = $start2 + $duration2;

        // Check for overlap: (start1 < end2) && (start2 < end1)
        return ($start1 < $end2) && ($start2 < $end1);
    }

    /**
     * Convert time string (H:i) to minutes since midnight
     * 
     * @param string $time Time in H:i format
     * @return int Minutes since midnight
     */
    private static function timeToMinutes($time)
    {
        $parts = explode(':', $time);
        return (int)$parts[0] * 60 + (int)$parts[1];
    }

    /**
     * Get schedule conflicts for a specific schedule
     * 
     * @param string $scheduleId
     * @return array Array of conflicting schedules
     */
    public static function getConflictsForSchedule($scheduleId)
    {
        $schedule = self::find($scheduleId);
        if (!$schedule) {
            return [];
        }

        return self::checkForOverlappingSchedules($schedule, $scheduleId);
    }

    /**
     * Get all schedule conflicts in the system
     * 
     * @return array Array of schedule conflicts
     */
    public static function getAllConflicts()
    {
        $conflicts = [];
        $schedules = self::where('is_active', true)->get();

        foreach ($schedules as $schedule) {
            $scheduleConflicts = self::checkForOverlappingSchedules($schedule, $schedule->id);
            if (!empty($scheduleConflicts)) {
                $conflicts[] = [
                    'schedule' => $schedule,
                    'conflicts' => $scheduleConflicts
                ];
            }
        }

        return $conflicts;
    }
}
