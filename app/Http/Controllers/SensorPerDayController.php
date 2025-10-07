<?php

namespace App\Http\Controllers;

use App\Models\WaterLevel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SensorPerDayController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'web']);
    }

    public function show(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json(['error' => 'Unauthenticated.'], 401);
            }

            // Get the requested date or use today
            $date = $request->query('date', now()->toDateString());
            
            // Get start and end of the requested date
            $startOfDay = Carbon::parse($date)->startOfDay();
            $endOfDay = Carbon::parse($date)->endOfDay();

            // Get all records for the date
            $records = WaterLevel::whereBetween('created_at', [$startOfDay, $endOfDay])
                ->orderBy('created_at', 'desc')
                ->get();

            // If no records found, return empty response
            if ($records->isEmpty()) {
                return response()->json([
                    'daily_stats' => null,
                    'history' => []
                ]);
            }

            // Calculate simple daily stats
            $stats = [
                'total_readings' => $records->count(),
                'water_level' => [
                    'average' => round($records->avg('water_level'), 2),
                    'min' => round($records->min('water_level'), 2),
                    'max' => round($records->max('water_level'), 2)
                ],
                'soil_moisture' => [
                    'average' => round($records->avg('soil_moisture'), 2),
                    'min' => round($records->min('soil_moisture'), 2),
                    'max' => round($records->max('soil_moisture'), 2)
                ],
                'temperature' => [
                    'average' => round($records->avg('temperature'), 2),
                    'min' => round($records->min('temperature'), 2),
                    'max' => round($records->max('temperature'), 2)
                ],
                'humidity' => [
                    'average' => round($records->avg('humidity'), 2),
                    'min' => round($records->min('humidity'), 2),
                    'max' => round($records->max('humidity'), 2)
                ]
            ];

            // Format history records
            $history = $records->map(function ($record) {
                return [
                    'time' => $record->created_at->format('h:i A'),
                    'water_level' => round($record->water_level, 2),
                    'soil_moisture' => round($record->soil_moisture, 2),
                    'temperature' => round($record->temperature, 2),
                    'humidity' => round($record->humidity, 2),
                    'status' => $record->status
                ];
            });

            return response()->json([
                'daily_stats' => $stats,
                'history' => $history
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching history: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to load history data. Please try again later.'
            ], 500);
        }
    }
} 