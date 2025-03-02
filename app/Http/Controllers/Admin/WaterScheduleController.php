<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaterSchedule;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WaterScheduleController extends Controller
{
    /**
     * Display the water schedule page.
     */
    public function index(): View
    {
        $schedules = WaterSchedule::orderBy('start_time')->get();
        return view('admin.waterschedule.index', compact('schedules'));
    }

    /**
     * Store a new water schedule.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'days' => 'required|array',
                'days.*' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'
            ]);

            $schedule = WaterSchedule::create([
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'days' => $validated['days'],
                'status' => 'active'
            ]);

            Log::info('Water schedule created', ['schedule' => $schedule]);

            return response()->json([
                'message' => 'Schedule created successfully',
                'schedule' => $schedule
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating water schedule', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error creating schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a water schedule.
     */
    public function update(Request $request, WaterSchedule $waterSchedule): JsonResponse
    {
        try {
            $validated = $request->validate([
                'start_time' => 'required|date_format:H:i',
                'end_time' => 'required|date_format:H:i|after:start_time',
                'days' => 'required|array',
                'days.*' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'status' => 'required|in:active,inactive'
            ]);

            $waterSchedule->update($validated);
            Log::info('Water schedule updated', ['schedule' => $waterSchedule]);

            return response()->json([
                'message' => 'Schedule updated successfully',
                'schedule' => $waterSchedule
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating water schedule', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error updating schedule: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a water schedule.
     */
    public function destroy(WaterSchedule $waterSchedule): JsonResponse
    {
        try {
            $waterSchedule->delete();
            Log::info('Water schedule deleted', ['schedule_id' => $waterSchedule->id]);

            return response()->json([
                'message' => 'Schedule deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting water schedule', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error deleting schedule: ' . $e->getMessage()
            ], 500);
        }
    }
} 