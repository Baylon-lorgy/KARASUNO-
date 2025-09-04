<?php

namespace App\Http\Controllers;

use App\Models\SensorPerDay;
use App\Models\WaterLevel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index(Request $request)
    {
        try {
            if (!Auth::check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Get today's date in the correct timezone
            $today = Carbon::now()->setTimezone('Asia/Manila')->toDateString();
            $date = $request->query('date', $today);
            
            Log::info('Fetching history data for date: ' . $date . ' for user: ' . Auth::id());
            
            // Get records for the specified date
            $records = WaterLevel::forDate($date)->get();
            
            if ($records->isEmpty()) {
                Log::info('No records found for date: ' . $date);
                return response()->json([
                    'history' => [],
                    'stats' => [
                        'total_readings' => 0,
                        'avg_water_level' => 0,
                        'avg_soil_moisture' => 0,
                        'avg_temperature' => 0,
                        'avg_humidity' => 0
                    ]
                ]);
            }
            
            // Format history records
            $history = $records->map(function ($record) {
                return [
                    'created_at' => $record->created_at->setTimezone('Asia/Manila')->toISOString(),
                    'water_level' => $record->water_level,
                    'soil_moisture' => $record->soil_moisture,
                    'temperature' => $record->temperature,
                    'humidity' => $record->humidity,
                    'status' => $record->status
                ];
            })->sortByDesc('created_at')->values();
            
            // Calculate statistics
            $stats = [
                'total_readings' => $records->count(),
                'avg_water_level' => $records->avg('water_level'),
                'avg_soil_moisture' => $records->avg('soil_moisture'),
                'avg_temperature' => $records->avg('temperature'),
                'avg_humidity' => $records->avg('humidity')
            ];
            
            return response()->json([
                'history' => $history,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching history data: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to fetch history data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'water_level' => 'required|numeric',
                'soil_moisture' => 'required|numeric',
                'temperature' => 'required|numeric',
                'humidity' => 'required|numeric',
                'water_float_status' => 'required|string'
            ]);

            // Create a new water level record
            $waterLevel = new WaterLevel([
                'water_level' => (float)$data['water_level'],
                'soil_moisture' => (float)$data['soil_moisture'],
                'temperature' => (float)$data['temperature'],
                'humidity' => (float)$data['humidity'],
                'status' => $data['water_float_status']
            ]);

            $waterLevel->save();

            Log::info('Sensor data stored in water_levels', [
                'id' => $waterLevel->_id,
                'water_level' => $waterLevel->water_level,
                'soil_moisture' => $waterLevel->soil_moisture,
                'temperature' => $waterLevel->temperature,
                'humidity' => $waterLevel->humidity,
                'status' => $waterLevel->status
            ]);

            return response()->json([
                'message' => 'Sensor data stored successfully',
                'data' => $waterLevel
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing sensor data: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Failed to store sensor data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 