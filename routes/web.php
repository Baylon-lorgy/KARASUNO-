<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\WaterLevelController;
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\SensorHistoryController;
use App\Http\Controllers\SensorReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WaterScheduleController;
use App\Http\Controllers\SystemManagementController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use Inertia\Inertia;

// Broadcasting routes must be registered first
Broadcast::routes();

// Public routes
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'auth' => [
            'user' => auth()->user(),
            'token' => session('auth_token')
        ]
    ]);
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
});

// Protected routes
Route::middleware('auth')->group(function () {
    // Admin Dashboard
    Route::prefix('admin')->group(function () {
        // Dashboard
        Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard', [
            'auth' => [
                'user' => auth()->user(),
                'token' => session('auth_token')
            ]
        ]);
    })->name('admin.dashboard');

    // System Logs
        Route::get('/systemlogs', function () {
        return Inertia::render('Admin/SystemLogs', [
            'auth' => [
                'user' => auth()->user(),
                'token' => session('auth_token')
            ]
        ]);
    })->name('admin.systemlogs');

    // Water Schedule
        Route::get('/waterschedule', function () {
        return Inertia::render('Admin/WaterSchedule', [
            'auth' => [
                'user' => auth()->user(),
                'token' => session('auth_token')
            ]
        ]);
    })->name('admin.waterschedule');

    // History
        Route::get('/sensor-history', [SensorHistoryController::class, 'index'])->name('admin.sensor.history');

    // Reports
        Route::get('/sensor-report', [SensorReportController::class, 'index'])->name('admin.sensor.report');
        Route::get('/reports/sensor', [SensorReportController::class, 'generateReport'])->name('admin.reports.sensor');
        
        // PDF Reports
        Route::get('/pdf-report', [ReportController::class, 'generateReport'])->name('admin.pdf.report');
        Route::get('/pdf-simple-report', [ReportController::class, 'generateSimpleReport'])->name('admin.pdf.simple');

        // Audit Logs
        Route::get('/audit-logs', function () {
            return Inertia::render('Admin/AuditLogs', [
                'auth' => [
                    'user' => auth()->user(),
                    'token' => session('auth_token')
                ]
            ]);
        })->name('admin.audit.logs');

        // User Management
        Route::get('/user-management', function () {
            return Inertia::render('Admin/UserManagement', [
                'auth' => [
                    'user' => auth()->user(),
                    'token' => session('auth_token')
                ]
            ]);
        })->name('admin.user.management');

        // System Management
        Route::get('/system-management', [SystemManagementController::class, 'index'])->name('admin.system-management');
    });

    // Water Levels
    Route::get('/water-levels', [WaterLevelController::class, 'index'])->name('water-levels.index');
    Route::post('/water-levels', [WaterLevelController::class, 'store'])->name('water-levels.store');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Auth
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Sensor Updates
    Route::post('/sensor/update', [SensorController::class, 'update'])->name('sensor.update');
    Route::post('/sensor/watering-status', [SensorController::class, 'wateringStatus'])->name('sensor.watering-status');

    // Sensor routes
    Route::get('/api/sensor-data', [SensorController::class, 'getSensorData']);
    Route::post('/api/sensor-data', [SensorController::class, 'storeSensorData']);

    // Notification routes
    Route::post('/api/notifications/send-email', [NotificationController::class, 'sendEmail']);
    Route::get('/api/notifications/settings', [NotificationController::class, 'getNotificationSettings']);
    Route::post('/api/notifications/settings', [NotificationController::class, 'updateNotificationSettings']);
});

require __DIR__.'/auth.php';
