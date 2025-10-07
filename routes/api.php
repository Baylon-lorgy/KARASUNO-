<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WaterLevelController;
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SystemLogController;
use App\Http\Controllers\SensorPerDayController;
use App\Http\Controllers\WateringController;
use App\Http\Controllers\SensorReadingController;
use App\Http\Controllers\WateringScheduleController;
use App\Http\Controllers\WateringRuleController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\SystemManagementController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\WaterScheduleController;
use App\Http\Controllers\StaffUserController;
use App\Http\Controllers\Api\WateringHistoryController;
use Illuminate\Support\Facades\Http; // Added for Http facade
use Illuminate\Support\Facades\Log; // Added for Log facade

// CSRF token refresh endpoint
Route::get('/csrf-token', function () {
    return response()->json([
        'csrf_token' => csrf_token(),
        'session_token' => session()->token()
    ]);
});

// Public API routes
Route::post('/water-levels', [WaterLevelController::class, 'store']);
Route::post('/sensor-data/update', [SensorDataController::class, 'update']);
Route::get('/sensor-data/latest', [SensorDataController::class, 'latest']);

// Sensor Data Routes (Public)
Route::get('/sensor-data', [SensorDataController::class, 'getSensorData']);
Route::post('/sensor-data', [SensorController::class, 'storeSensorData']);

// Real sensor data endpoint
Route::get('/sensor-data-real', function() {
    try {
        // Get the latest sensor data directly from MongoDB
        $connection = \DB::connection('mongodb');
        $collection = $connection->getMongoDB()->selectCollection('sensor_readings');
        
        // Get the latest document
        $latestReading = $collection->findOne(
            [],
            ['sort' => ['created_at' => -1]]
        );
        
        if ($latestReading) {
            return response()->json([
                'soil_moisture' => (float)$latestReading->soil_moisture,
                'temperature' => (float)$latestReading->temperature,
                'humidity' => (float)$latestReading->humidity,
                'water_level' => 0,
                'watering_active' => false,
                'remaining_time' => 0,
                'thresholds' => [
                    'soil_moisture' => 30,
                    'temperature' => 30,
                    'humidity' => 40
                ]
            ]);
        } else {
            // Fallback to static data if no readings found
            return response()->json([
                'soil_moisture' => 45.5,
                'temperature' => 25.2,
                'humidity' => 60.8,
                'water_level' => 0,
                'watering_active' => false,
                'remaining_time' => 0,
                'thresholds' => [
                    'soil_moisture' => 30,
                    'temperature' => 30,
                    'humidity' => 40
                ]
            ]);
        }
    } catch (\Exception $e) {
        Log::error('Error fetching real sensor data: ' . $e->getMessage());
        
        // Return fallback data
        return response()->json([
            'soil_moisture' => 45.5,
            'temperature' => 25.2,
            'humidity' => 60.8,
            'water_level' => 0,
            'watering_active' => false,
            'remaining_time' => 0,
            'thresholds' => [
                'soil_moisture' => 30,
                'temperature' => 30,
                'humidity' => 40
            ]
        ]);
    }
});

// Check available sensor data
Route::get('/sensor-data-check', function() {
    try {
        // Check if there are any sensor readings in the database
        $sensorCount = \App\Models\SensorReading::count();
        $latestReading = \App\Models\SensorReading::latest()->first();
        
        return response()->json([
            'total_sensor_readings' => $sensorCount,
            'latest_reading' => $latestReading ? [
                'soil_moisture' => $latestReading->soil_moisture,
                'temperature' => $latestReading->temperature,
                'humidity' => $latestReading->humidity,
                'created_at' => $latestReading->created_at
            ] : null,
            'message' => $sensorCount > 0 ? 'Sensor data available' : 'No sensor data found'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to check sensor data',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Simple MongoDB test
Route::get('/mongodb-test', function() {
    try {
        $connection = \DB::connection('mongodb');
        $collection = $connection->getMongoDB()->selectCollection('sensor_readings');
        $count = $collection->countDocuments();
        
        return response()->json([
            'success' => true,
            'collection_count' => $count,
            'collection_name' => 'sensor_readings',
            'mongodb_working' => true
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'MongoDB test failed',
            'message' => $e->getMessage(),
            'mongodb_working' => false
        ], 500);
    }
});

// Simulate sensor data for testing
Route::post('/simulate-sensor-data', function(Request $request) {
    try {
        $data = $request->validate([
            'soil_moisture' => 'required|numeric|between:0,100',
            'temperature' => 'required|numeric|between:-10,50',
            'humidity' => 'required|numeric|between:0,100'
        ]);
        
        // Create a sensor reading
        $reading = \App\Models\SensorReading::create($data);
        
        return response()->json([
            'success' => true,
            'message' => 'Sensor data simulated successfully',
            'data' => $reading
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to simulate sensor data',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Create sensor data directly in MongoDB
Route::post('/create-sensor-data', function(Request $request) {
    try {
        $data = $request->validate([
            'soil_moisture' => 'required|numeric|between:0,100',
            'temperature' => 'required|numeric|between:-10,50',
            'humidity' => 'required|numeric|between:0,100'
        ]);
        
        // Add timestamp
        $data['created_at'] = now();
        $data['updated_at'] = now();
        
        // Insert directly into MongoDB collection
        $connection = \DB::connection('mongodb');
        $collection = $connection->getMongoDB()->selectCollection('sensor_readings');
        $result = $collection->insertOne($data);
        
        return response()->json([
            'success' => true,
            'message' => 'Sensor data created successfully',
            'inserted_id' => $result->getInsertedId(),
            'data' => $data
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to create sensor data',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Create sample watering history data
Route::post('/create-watering-history', function(Request $request) {
    try {
        $data = $request->validate([
            'action' => 'required|string|in:start,stop',
            'source' => 'required|string|in:manual,automatic,scheduled',
            'duration' => 'nullable|integer|min:1|max:5',
            'reason' => 'nullable|string|max:255'
        ]);
        
        // Add timestamp
        $data['created_at'] = now();
        $data['updated_at'] = now();
        
        // Insert directly into MongoDB collection
        $connection = \DB::connection('mongodb');
        $collection = $connection->getMongoDB()->selectCollection('watering_history');
        $result = $collection->insertOne($data);
        
        return response()->json([
            'success' => true,
            'message' => 'Watering history created successfully',
            'inserted_id' => $result->getInsertedId(),
            'data' => $data
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Failed to create watering history',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Watering Control Routes - Make these public for ESP32 access
Route::post('/watering-control', [WateringController::class, 'control']);
Route::get('/watering-status', [WateringController::class, 'status']); // This is the endpoint the ESP32 should use

// Watering History Routes (Public)
Route::get('/watering-history', [WateringHistoryController::class, 'index']);
Route::post('/watering-history', [WateringHistoryController::class, 'store']);
Route::get('/watering-history/{id}', [WateringHistoryController::class, 'show']);
Route::delete('/watering-history/{id}', [WateringHistoryController::class, 'destroy']);

// Protected API routes
Route::middleware(['auth:sanctum', 'web'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/water-levels', [WaterLevelController::class, 'index']);
    
    // History routes
    Route::get('/history', [HistoryController::class, 'index']);
    Route::post('/history', [HistoryController::class, 'store']);
    
    // Daily sensor stats route
    Route::get('/sensor-per-day', [SensorPerDayController::class, 'show']);
    
    // Report routes
    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports', [ReportController::class, 'generate']);
    
    // Notification routes
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);
    Route::post('/notifications', [NotificationController::class, 'store']);
    
    // System Notifications routes (for DashboardLayout)
    Route::get('/system-notifications', [NotificationController::class, 'index']);
    Route::post('/system-notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/system-notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/system-notifications/{id}', [NotificationController::class, 'destroy']);
    Route::post('/system-notifications', [NotificationController::class, 'store']);
    
    // System Logs Routes
    Route::get('/system-logs', [SystemLogController::class, 'index']);
    Route::post('/system-logs', [SystemLogController::class, 'store']);

    // Watering Schedule Routes
    Route::get('/watering-schedules', [WateringScheduleController::class, 'index']);
    Route::post('/watering-schedules', [WateringScheduleController::class, 'store']);
    Route::put('/watering-schedules/{id}', [WateringScheduleController::class, 'update']);
    Route::delete('/watering-schedules/{id}', [WateringScheduleController::class, 'destroy']);
    Route::patch('/watering-schedules/{id}/toggle', [WateringScheduleController::class, 'toggle']);
    Route::get('/watering-schedules/conflicts', [WateringScheduleController::class, 'getConflicts']);
    Route::post('/watering-schedules/check-conflicts', [WateringScheduleController::class, 'checkConflicts']);

    // Watering Rules Routes
    Route::get('/watering-rules', [WateringRuleController::class, 'index']);
    Route::post('/watering-rules', [WateringRuleController::class, 'store']);
    Route::put('/watering-rules/{id}', [WateringRuleController::class, 'update']);
    Route::delete('/watering-rules/{id}', [WateringRuleController::class, 'destroy']);
    Route::post('/watering-rules/{id}/toggle', [WateringRuleController::class, 'toggle']);

    // Water Schedule Routes
    Route::get('/water-schedules', [WaterScheduleController::class, 'index']);
    Route::post('/water-schedules', [WaterScheduleController::class, 'store']);
    Route::put('/water-schedules/{schedule}', [WaterScheduleController::class, 'update']);
    Route::delete('/water-schedules/{schedule}', [WaterScheduleController::class, 'destroy']);
    Route::post('/water-schedules/{schedule}/start', [WaterScheduleController::class, 'startWatering']);
});

// Test route
Route::get('/test', function() {
    return response()->json(['status' => 'ok', 'message' => 'Server is running']);
});

// Debug sensor data route
Route::get('/debug-sensor-api', function() {
    try {
        $controller = new \App\Http\Controllers\SensorController();
        return $controller->getSensorData();
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'SensorController failed',
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Simple API test
Route::get('/api-test', function() {
    return response()->json([
        'status' => 'API is working',
        'timestamp' => now()->toISOString()
    ]);
});

// Static sensor data test
Route::get('/sensor-data-static', function() {
    return response()->json([
        'soil_moisture' => 45.5,
        'temperature' => 25.2,
        'humidity' => 60.8,
        'water_level' => 0,
        'watering_active' => false,
        'remaining_time' => 0,
        'thresholds' => [
            'soil_moisture' => 30,
            'temperature' => 30,
            'humidity' => 40
        ]
    ]);
});

// Test watering control
Route::get('/test-watering-control', function() {
    try {
        $controller = app(\App\Http\Controllers\WateringController::class);
        return response()->json([
            'success' => true,
            'message' => 'WateringController is accessible'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'WateringController failed',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Test route
Route::get('/test-mongo', function() {
    try {
        $waterLevels = \App\Models\WaterLevel::all();
        return response()->json([
            'status' => 'success',
            'count' => $waterLevels->count(),
            'data' => $waterLevels
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});

// Sensor readings routes
Route::post('/sensor-readings', [SensorReadingController::class, 'store']);
Route::get('/sensor-readings', [SensorReadingController::class, 'index']);

// Sensor routes
Route::middleware(['auth:web'])->group(function () {
    Route::get('/sensor/data', [SensorController::class, 'getSensorData']);
    Route::post('/sensor/data', [SensorController::class, 'storeSensorData']);
    Route::post('/sensor/thresholds', [SensorController::class, 'updateThresholds']);
});

// System Management Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/system-status', [SystemManagementController::class, 'getSystemStatus']);
    Route::get('/maintenance-logs', [SystemManagementController::class, 'getMaintenanceLogs']);
    Route::post('/notification-settings', [SystemManagementController::class, 'updateNotificationSettings']);
    Route::post('/alert-thresholds', [SystemManagementController::class, 'updateAlertThresholds']);
    Route::post('/system-restart', [SystemManagementController::class, 'restartSystem']);
});

// Staff User Management Routes
Route::middleware('auth:web')->group(function () {
    Route::get('/staff-users', [StaffUserController::class, 'index']);
    Route::post('/staff-users', [StaffUserController::class, 'store']);
    Route::get('/staff-users/roles', [StaffUserController::class, 'getRoles']);
    Route::get('/staff-users/permissions', [StaffUserController::class, 'getPermissions']);
    Route::get('/staff-users/{id}', [StaffUserController::class, 'show']);
    Route::put('/staff-users/{id}', [StaffUserController::class, 'update']);
    Route::delete('/staff-users/{id}', [StaffUserController::class, 'destroy']);
    Route::post('/staff-users/{id}/approve', [StaffUserController::class, 'approve']);
    Route::post('/staff-users/{id}/resend-invitation', [StaffUserController::class, 'resendInvitation']);
});

// Fallback for development: demo notifications if none exist
Route::get('/system-notifications-demo', function () {
    return response()->json([
        [
            '_id' => 'demo1',
            'type' => 'system_info',
            'message' => 'This is a demo notification.',
            'priority' => 'medium',
            'read' => false,
            'created_at' => now(),
            'data' => null
        ],
        [
            '_id' => 'demo2',
            'type' => 'system_alert',
            'message' => 'System alert: Check your sensors!',
            'priority' => 'high',
            'read' => false,
            'created_at' => now(),
            'data' => ['sensor' => 'temperature']
        ]
    ]);
});
