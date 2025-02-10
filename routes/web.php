<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/tables', function () {
    return view('tables');
})->middleware(['auth', 'verified'])->name('tables');

Route::get('/account', function () {
    return view('account');
})->middleware(['auth'])->name('account');

Route::get('/notifications', function () {
    return view('notifications');
})->middleware(['auth'])->name('notifications');



Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/account', [AccountController::class, 'destroy'])->name('account.destroy');
});

require __DIR__.'/auth.php';
