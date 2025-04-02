<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\WaterLevelController;
use App\Http\Controllers\SensorDataController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public routes
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'auth' => auth()->user(),
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
    Route::get('/admin/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('admin.dashboard');

    // System Logs
    Route::get('/admin/systemlogs', function () {
        return Inertia::render('Admin/SystemLogs');
    })->name('admin.systemlogs');

    // Water Schedule
    Route::get('/admin/waterschedule', function () {
        return Inertia::render('Admin/WaterSchedule');
    })->name('admin.waterschedule');

    // History
    Route::get('/admin/history', function () {
        return Inertia::render('Admin/History');
    })->name('admin.history');

    // Reports
    Route::get('/admin/reports', function () {
        return Inertia::render('Admin/Reports');
    })->name('admin.reports');

    // Water Levels
    Route::get('/water-levels', [WaterLevelController::class, 'index'])->name('water-levels.index');
    Route::post('/water-levels', [WaterLevelController::class, 'store'])->name('water-levels.store');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Auth
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
});

require __DIR__.'/auth.php';
