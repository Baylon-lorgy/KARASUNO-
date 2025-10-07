<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\WateringHistory;
use App\Events\WateringStarted;
use App\Events\WateringStopped;
use App\Services\EmailNotificationService;

class WateringController extends Controller
{
    const CACHE_KEY = 'watering_state';
    const CACHE_TTL = 3600; // 1 hour

    protected $emailService;

    public function __construct(EmailNotificationService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function control(Request $request)
    {
        try {
            $validated = $request->validate([
                'should_water' => 'required|boolean',
                'duration' => 'nullable|integer|min:1|max:5',
                'source' => 'nullable|string|in:manual,automatic,scheduled'
            ]);

            $state = Cache::get(self::CACHE_KEY, [
                'watering_active' => false,
                'remaining_time' => 0,
                'start_time' => null,
                'duration' => 0
            ]);

            $source = $validated['source'] ?? 'manual';

            if (!$validated['should_water']) {
                // Start watering
                if (!isset($validated['duration'])) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Duration is required when starting watering'
                    ], 422);
                }
                $state['watering_active'] = true;
                $state['start_time'] = now()->timestamp;
                $state['duration'] = $validated['duration'];
                $state['remaining_time'] = $validated['duration'] * 60;

                // Log watering start (with error handling)
                try {
                    WateringHistory::create([
                        'action' => 'start',
                        'source' => $source,
                        'duration' => $validated['duration'],
                        'reason' => ucfirst($source) . ' watering started'
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to create watering history: ' . $e->getMessage());
                }

                // Broadcast watering started event (with error handling)
                try {
                    broadcast(new WateringStarted('main', now(), $validated['duration'], $source))->toOthers();
                } catch (\Exception $e) {
                    \Log::error('Failed to broadcast watering started: ' . $e->getMessage());
                }

                // Send email alert (with error handling)
                try {
                    $this->emailService->sendWateringAlert('started', $validated['duration'], $source);
                } catch (\Exception $e) {
                    \Log::error('Failed to send watering alert: ' . $e->getMessage());
                }
            } else {
                // Stop watering
                $state['watering_active'] = false;
                $state['remaining_time'] = 0;
                $state['start_time'] = null;
                $state['duration'] = 0;

                // Log watering stop (with error handling)
                try {
                    WateringHistory::create([
                        'action' => 'stop',
                        'source' => $source,
                        'duration' => null,
                        'reason' => ucfirst($source) . ' watering stopped'
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Failed to create watering history: ' . $e->getMessage());
                }

                // Broadcast watering stopped event (with error handling)
                try {
                    broadcast(new WateringStopped('main', now(), $source))->toOthers();
                } catch (\Exception $e) {
                    \Log::error('Failed to broadcast watering stopped: ' . $e->getMessage());
                }

                // Send email alert (with error handling)
                try {
                    $this->emailService->sendWateringAlert('stopped', null, $source);
                } catch (\Exception $e) {
                    \Log::error('Failed to send watering alert: ' . $e->getMessage());
                }
            }

            Cache::put(self::CACHE_KEY, $state, self::CACHE_TTL);

            return response()->json([
                'success' => true,
                'watering_active' => $state['watering_active'],
                'remaining_time' => $state['remaining_time'],
                'duration' => $state['duration']
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Watering control error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function status()
    {
            $state = Cache::get(self::CACHE_KEY, [
                'watering_active' => false,
                'remaining_time' => 0,
                'start_time' => null,
                'duration' => 0
            ]);

        // --- SCHEDULE LOGIC ---
        $now = now()->setTimezone('Asia/Manila');
        $currentTime = $now->format('H:i');
        $currentDay = $now->dayOfWeekIso % 7; // 0=Sun, 6=Sat

        $scheduleMatch = null;
        $duration = 0;
        $wateringActive = false;

        $schedules = \App\Models\WateringSchedule::where('is_active', true)->get();
        foreach ($schedules as $sch) {
            if ($sch->start_time === $currentTime && in_array($currentDay, $sch->days_of_week)) {
                $scheduleMatch = $sch;
                $duration = $sch->duration;
                $wateringActive = true;
                break;
            }
        }

        if ($wateringActive) {
            $state['watering_active'] = true;
            $state['duration'] = $duration;
            $state['remaining_time'] = $duration * 60;
        } else {
            // If not scheduled, keep previous logic
            if ($state['watering_active'] && $state['start_time']) {
                    $elapsed = now()->timestamp - $state['start_time'];
                $total = $state['duration'] * 60;
                $remaining = max(0, $total - $elapsed);
                if ($remaining <= 0) {
                        $state['watering_active'] = false;
                    $state['remaining_time'] = 0;
                        $state['start_time'] = null;
                        $state['duration'] = 0;
                    Cache::put(self::CACHE_KEY, $state, self::CACHE_TTL);
                } else {
                    $state['remaining_time'] = $remaining;
                }
                }
            }

            return response()->json([
                'success' => true,
                'should_water' => !$state['watering_active'],
                'watering_active' => $state['watering_active'],
                'remaining_time' => $state['remaining_time'],
                'duration' => $state['duration']
            ]);
    }
} 