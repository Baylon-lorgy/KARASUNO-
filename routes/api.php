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

// Public API routes
Route::post('/water-levels', [WaterLevelController::class, 'store']);
Route::post('/sensor-data/update', [SensorDataController::class, 'update']);
Route::get('/sensor-data/latest', [SensorDataController::class, 'latest']);

// Watering Control Routes - Make these public for ESP32 access
Route::post('/watering-control', [WateringController::class, 'control']);
Route::get('/watering-status', [WateringController::class, 'status']); // This is the endpoint the ESP32 should use

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
