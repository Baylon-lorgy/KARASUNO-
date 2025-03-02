<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\WaterScheduleController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Admin\SystemLogController;
use App\Http\Controllers\Admin\AuthController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('homepage');

// Guest routes (unauthenticated users only)
Route::middleware('guest')->group(function () {
    // Admin login routes
    Route::get('admin/login', [AuthController::class, 'showLoginForm'])
        ->name('admin.login');
    Route::post('admin/login', [AuthController::class, 'login'])
        ->name('admin.login.store');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware('auth:admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Water Schedule
    Route::resource('water-schedule', WaterScheduleController::class)->names([
        'index' => 'water.schedule',
        'store' => 'water.schedule.store',
        'update' => 'water.schedule.update',
        'destroy' => 'water.schedule.destroy',
    ]);
    
    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history');
    Route::get('/history/export', [HistoryController::class, 'export'])->name('history.export');
    
    // System Logs
    Route::get('/logs', [SystemLogController::class, 'index'])->name('logs');
    Route::get('/logs/download', [SystemLogController::class, 'download'])->name('logs.download');
    Route::post('/logs/clear', [SystemLogController::class, 'clear'])->name('logs.clear');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
