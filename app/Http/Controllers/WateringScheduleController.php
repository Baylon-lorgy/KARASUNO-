<?php

namespace App\Http\Controllers;

use App\Models\WateringSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use MongoDB\Laravel\Collection;

class WateringScheduleController extends Controller
{
    public function index()
    {
        try {
            $schedules = WateringSchedule::orderBy('start_time')->get();
            return response()->json($schedules);
        } catch (\Exception $e) {
            Log::error('Error fetching watering schedules: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch watering schedules'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_time' => 'required|date_format:H:i',
                'duration' => 'required|integer|min:1|max:5',
                'days_of_week' => 'required|array|min:1',
                'days_of_week.*' => 'integer|min:0|max:6',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'details' => $validator->errors()
                ], 422);
            }

            $data = $request->all();
            $data['start_time'] = date('H:i', strtotime($data['start_time']));
            
            // Check for overlapping schedules before creating
            $tempSchedule = new WateringSchedule($data);
            $conflicts = WateringSchedule::checkForOverlappingSchedules($tempSchedule);
            
            if (!empty($conflicts)) {
                $conflictDetails = collect($conflicts)->map(function($conflict) {
                    $days = collect($conflict->days_of_week)->map(function($day) {
                        $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                        return $dayNames[(int)$day] ?? $day;
                    })->implode(', ');
                    return [
                        'time' => $conflict->start_time,
                        'duration' => $conflict->duration,
                        'days' => $days,
                        'id' => $conflict->id
                    ];
                })->toArray();

                return response()->json([
                    'error' => 'Schedule conflicts detected',
                    'type' => 'overlap_conflict',
                    'conflicts' => $conflictDetails,
                    'message' => 'This schedule conflicts with existing schedules. Please choose a different time or days.'
                ], 409); // 409 Conflict
            }
            
            $schedule = WateringSchedule::create($data);
            return response()->json($schedule, 201);
        } catch (\InvalidArgumentException $e) {
            Log::warning('Schedule validation error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Schedule validation failed',
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating watering schedule: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to create watering schedule',
                'message' => 'An unexpected error occurred. Please try again.'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $schedule = WateringSchedule::find($id);
            
            if (!$schedule) {
                return response()->json(['error' => 'Schedule not found'], 404);
            }

            $validator = Validator::make($request->all(), [
                'start_time' => 'required|date_format:H:i',
                'duration' => 'required|integer|min:1|max:5',
                'days_of_week' => 'required|array|min:1',
                'days_of_week.*' => 'integer|min:0|max:6',
                'is_active' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'details' => $validator->errors()
                ], 422);
            }

            $data = $request->all();
            $data['start_time'] = date('H:i', strtotime($data['start_time']));
            
            // Check for overlapping schedules before updating
            $tempSchedule = new WateringSchedule($data);
            $tempSchedule->id = $id; // Set ID for exclusion check
            $conflicts = WateringSchedule::checkForOverlappingSchedules($tempSchedule, $id);
            
            if (!empty($conflicts)) {
                $conflictDetails = collect($conflicts)->map(function($conflict) {
                    $days = collect($conflict->days_of_week)->map(function($day) {
                        $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                        return $dayNames[(int)$day] ?? $day;
                    })->implode(', ');
                    return [
                        'time' => $conflict->start_time,
                        'duration' => $conflict->duration,
                        'days' => $days,
                        'id' => $conflict->id
                    ];
                })->toArray();

                return response()->json([
                    'error' => 'Schedule conflicts detected',
                    'type' => 'overlap_conflict',
                    'conflicts' => $conflictDetails,
                    'message' => 'This schedule conflicts with existing schedules. Please choose a different time or days.'
                ], 409); // 409 Conflict
            }
            
            $schedule->update($data);
            return response()->json($schedule);
        } catch (\InvalidArgumentException $e) {
            Log::warning('Schedule validation error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Schedule validation failed',
                'message' => $e->getMessage()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating watering schedule: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to update watering schedule',
                'message' => 'An unexpected error occurred. Please try again.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $schedule = WateringSchedule::find($id);
            
            if (!$schedule) {
                return response()->json(['error' => 'Schedule not found'], 404);
            }

            $schedule->delete();
            return response()->json(null, 204);
        } catch (\Exception $e) {
            Log::error('Error deleting watering schedule: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete watering schedule'], 500);
        }
    }

    public function toggle($id)
    {
        try {
            $schedule = WateringSchedule::find($id);
            
            if (!$schedule) {
                return response()->json(['error' => 'Schedule not found'], 404);
            }

            $schedule->update(['is_active' => !$schedule->is_active]);
            return response()->json($schedule);
        } catch (\Exception $e) {
            Log::error('Error toggling watering schedule: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to toggle watering schedule'], 500);
        }
    }

    /**
     * Get all schedule conflicts in the system
     */
    public function getConflicts()
    {
        try {
            $conflicts = WateringSchedule::getAllConflicts();
            return response()->json([
                'conflicts' => $conflicts,
                'has_conflicts' => !empty($conflicts)
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching schedule conflicts: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch schedule conflicts'], 500);
        }
    }

    /**
     * Check for conflicts with a specific schedule (without saving)
     */
    public function checkConflicts(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'start_time' => 'required|date_format:H:i',
                'duration' => 'required|integer|min:1|max:5',
                'days_of_week' => 'required|array|min:1',
                'days_of_week.*' => 'integer|min:0|max:6',
                'exclude_id' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'Validation failed',
                    'details' => $validator->errors()
                ], 422);
            }

            $data = $request->all();
            $data['start_time'] = date('H:i', strtotime($data['start_time']));
            
            $tempSchedule = new WateringSchedule($data);
            $excludeId = $request->input('exclude_id');
            $conflicts = WateringSchedule::checkForOverlappingSchedules($tempSchedule, $excludeId);
            
            $conflictDetails = collect($conflicts)->map(function($conflict) {
                $days = collect($conflict->days_of_week)->map(function($day) {
                    $dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    return $dayNames[(int)$day] ?? $day;
                })->implode(', ');
                return [
                    'time' => $conflict->start_time,
                    'duration' => $conflict->duration,
                    'days' => $days,
                    'id' => $conflict->id
                ];
            })->toArray();

            return response()->json([
                'has_conflicts' => !empty($conflicts),
                'conflicts' => $conflictDetails,
                'message' => empty($conflicts) ? 'No conflicts detected' : 'Conflicts detected with existing schedules'
            ]);
        } catch (\Exception $e) {
            Log::error('Error checking schedule conflicts: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to check schedule conflicts'], 500);
        }
    }
}
