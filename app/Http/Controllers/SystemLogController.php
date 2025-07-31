<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SystemLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        try {
            $logs = SystemLog::orderBy('created_at', 'desc')
                ->take(50)
                ->get()
                ->map(function ($log) {
                    return [
                        'id' => $log->id,
                        'type' => $log->type,
                        'status' => $log->status,
                        'message' => $log->message,
                        'deviceId' => $log->device_id,
                        'details' => $log->details,
                        'timestamp' => $log->created_at->toISOString(),
                    ];
                });

            return response()->json($logs);
        } catch (\Exception $e) {
            Log::error('Failed to fetch system logs: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch system logs'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|string|in:connection,sensor,error,system',
                'status' => 'required|string|in:success,error,info,warning',
                'message' => 'required|string',
                'deviceId' => 'required|string',
                'details' => 'nullable|string',
            ]);

            // Convert deviceId to device_id for database
            $validated['device_id'] = $validated['deviceId'];
            unset($validated['deviceId']);

            $log = SystemLog::create($validated);

            // Broadcast the new log
            broadcast(new \App\Events\NewSystemLog($log))->toOthers();

            return response()->json([
                'id' => $log->id,
                'type' => $log->type,
                'status' => $log->status,
                'message' => $log->message,
                'deviceId' => $log->device_id,
                'details' => $log->details,
                'timestamp' => $log->created_at->toISOString(),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Failed to store system log: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to store system log'], 500);
        }
    }
} 