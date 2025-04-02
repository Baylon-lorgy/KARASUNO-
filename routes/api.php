<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WaterLevelController;
use App\Http\Controllers\SensorDataController;

// Public API routes
Route::post('/water-levels', [WaterLevelController::class, 'store']);
Route::post('/sensor-data/update', [SensorDataController::class, 'update']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/water-levels', [WaterLevelController::class, 'index']);
});
