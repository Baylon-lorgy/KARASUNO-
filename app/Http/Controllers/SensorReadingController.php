<?php

namespace App\Http\Controllers;

use App\Models\SensorReading;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SensorReadingController extends Controller
{
    public function store(Request $request)
    {
        try {
            $data = $request->validate([
                'water_level' => 'required|numeric',
                'soil_moisture' => 'required|numeric',
                'temperature' => 'required|numeric',
                'humidity' => 'required|numeric',
                'water_float_status' => 'required|string',
                'http_status' => 'required|integer',
                'response' => 'required|string',
                'timestamp' => 'required'
            ]);

            $reading = new SensorReading($data);
            $reading->save();

            Log::info('Sensor reading stored successfully', [
                'id' => $reading->_id,
                'http_status' => $reading->http_status,
                'timestamp' => $reading->timestamp
            ]);

            return response()->json([
                'message' => 'Sensor reading stored successfully',
                'data' => $reading
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing sensor reading: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'Failed to store sensor reading',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $date = $request->query('date', now()->toDateString());
            $readings = SensorReading::forDate($date)
                                   ->successful()
                                   ->orderBy('created_at', 'desc')
                                   ->get();

            return response()->json([
                'readings' => $readings,
                'stats' => [
                    'total' => $readings->count(),
                    'averages' => [
                        'water_level' => $readings->avg('water_level'),
                        'soil_moisture' => $readings->avg('soil_moisture'),
                        'temperature' => $readings->avg('temperature'),
                        'humidity' => $readings->avg('humidity')
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching sensor readings: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to fetch sensor readings',
                'message' => $e->getMessage()
            ], 500);
        }
    }
} 