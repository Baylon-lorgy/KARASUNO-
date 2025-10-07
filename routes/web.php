<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\WaterLevelController;
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\SensorHistoryController;
use App\Http\Controllers\SensorReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StaffInvitationController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\SensorController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WaterScheduleController;
use App\Http\Controllers\SystemManagementController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\StaffAuthController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
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

// Staff Invitation Routes (Public - no auth required)
Route::get('/staff/invitation/{token}', [StaffInvitationController::class, 'show'])->name('staff.invitation.show');
Route::post('/staff/invitation/{token}/accept', [StaffInvitationController::class, 'accept'])->name('staff.invitation.accept');

// Staff Authentication Routes (Public)
Route::get('/staff/login', [StaffAuthController::class, 'showLoginForm'])->name('staff.login');
Route::post('/staff/login', [StaffAuthController::class, 'login'])->name('staff.login.submit');
Route::post('/staff/logout', [StaffAuthController::class, 'logout'])->name('staff.logout');

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



        // Invitation Management Routes
        Route::get('/invitations', [InvitationController::class, 'index'])->name('admin.invitations.index');
        Route::get('/invitations/create', [InvitationController::class, 'create'])->name('admin.invitations.create');
        Route::post('/invitations', [InvitationController::class, 'store'])->name('admin.invitations.store');
        Route::put('/invitations/{id}', [InvitationController::class, 'update'])->name('admin.invitations.update');
        Route::get('/invitations/{id}/preview', [InvitationController::class, 'preview'])->name('admin.invitations.preview');
        Route::post('/invitations/{id}/resend', [InvitationController::class, 'resend'])->name('admin.invitations.resend');
        Route::post('/invitations/{id}/approve', [InvitationController::class, 'approve'])->name('admin.invitations.approve');
        Route::post('/invitations/{id}/deactivate', [InvitationController::class, 'deactivate'])->name('admin.invitations.deactivate');
        Route::post('/invitations/{id}/reactivate', [InvitationController::class, 'reactivate'])->name('admin.invitations.reactivate');
        Route::delete('/invitations/{id}/cancel', [InvitationController::class, 'cancel'])->name('admin.invitations.cancel');

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

    // Watering History Routes
    Route::get('/watering-history', [WateringHistoryController::class, 'index']);
    Route::post('/watering-history', [WateringHistoryController::class, 'store']);
    Route::get('/watering-history/{id}', [WateringHistoryController::class, 'show']);
    Route::delete('/watering-history/{id}', [WateringHistoryController::class, 'destroy']);

    // Notification Routes
    Route::get('/system-notifications', [NotificationController::class, 'index']);
    Route::post('/system-notifications', [NotificationController::class, 'store']);
    Route::post('/system-notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/system-notifications/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/system-notifications/{id}', [NotificationController::class, 'destroy']);
    Route::get('/notifications/settings', [NotificationController::class, 'getNotificationSettings']);
    Route::post('/notifications/settings', [NotificationController::class, 'updateNotificationSettings']);
});

// Staff Protected Routes
Route::middleware(\App\Http\Middleware\RedirectIfNotStaff::class)->group(function () {
    // Staff Dashboard
    Route::get('/staff/dashboard', [StaffAuthController::class, 'dashboard'])->name('staff.dashboard');
    
    // Staff API Routes
    Route::get('/staff/api/dashboard-data', [StaffAuthController::class, 'getDashboardData']);
    Route::post('/staff/api/watering-control', [StaffAuthController::class, 'wateringControl']);
    Route::get('/staff/api/sensor-history', [StaffAuthController::class, 'getSensorHistory']);
    Route::get('/staff/api/users', [StaffAuthController::class, 'getUsers']);
    Route::get('/staff/api/audit-logs', [StaffAuthController::class, 'getAuditLogs']);
    
    // Staff Pages (to be implemented)
    Route::get('/staff/water-control', [StaffAuthController::class, 'waterControl'])->name('staff.water-control');
    Route::get('/staff/sensor-history', [StaffAuthController::class, 'sensorHistory'])->name('staff.sensor-history');
    Route::get('/staff/user-management', [StaffAuthController::class, 'userManagement'])->name('staff.user-management');
    Route::get('/staff/audit-logs', [StaffAuthController::class, 'auditLogs'])->name('staff.audit-logs');
});

// Test email notifications
Route::get('/test-email', function () {
    $emailService = app(\App\Services\EmailNotificationService::class);
    
    // Test watering alert
    $result = $emailService->sendWateringAlert('started', 5, 'manual');
    
    return response()->json([
        'success' => $result,
        'message' => $result ? 'Email sent successfully' : 'Email failed to send'
    ]);
})->middleware('auth');

// Test sensor data
Route::get('/test-sensor-data', function () {
    try {
        $sensorController = app(\App\Http\Controllers\SensorController::class);
        return $sensorController->getSensorData();
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Test failed',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
})->middleware('auth');

// Simple test route without auth
Route::get('/debug-sensor', function () {
    try {
        // Test MongoDB connection
        $sensorCount = \App\Models\SensorReading::count();
        $thresholdCount = \App\Models\WateringThreshold::count();
        
        return response()->json([
            'success' => true,
            'sensor_readings_count' => $sensorCount,
            'thresholds_count' => $thresholdCount,
            'mongodb_connection' => 'working'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'MongoDB test failed',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Basic test route
Route::get('/test-basic', function () {
    return response()->json([
        'message' => 'Laravel is working',
        'timestamp' => now()->toISOString()
    ]);
});

// MongoDB connection test
Route::get('/test-mongodb', function () {
    try {
        // Test basic MongoDB connection
        $connection = \DB::connection('mongodb');
        $ping = $connection->getMongoDB()->command(['ping' => 1]);
        
        return response()->json([
            'success' => true,
            'mongodb_connection' => 'working',
            'ping_result' => $ping->toArray()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'MongoDB connection failed',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Test SensorReading model
Route::get('/test-sensor-model', function () {
    try {
        $count = \App\Models\SensorReading::count();
        
        return response()->json([
            'success' => true,
            'sensor_readings_count' => $count,
            'model_working' => true
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'SensorReading model failed',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Test MongoDB collection directly
Route::get('/test-collection', function () {
    try {
        $connection = \DB::connection('mongodb');
        $collection = $connection->getMongoDB()->selectCollection('sensor_readings');
        $count = $collection->countDocuments();
        
        return response()->json([
            'success' => true,
            'collection_count' => $count,
            'collection_name' => 'sensor_readings'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Collection test failed',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

require __DIR__.'/auth.php';
