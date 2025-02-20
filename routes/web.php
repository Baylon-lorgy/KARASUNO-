<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

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

Route::post('login', [LoginController::class, 'login'])->name('login');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [RegisterController::class, 'register']);

Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

Route::post('/sprinkler/schedule', [SprinklerController::class, 'schedule'])->name('sprinkler.schedule');

Route::get('/verify-email', [VerificationController::class, 'verifyEmail'])->name('verification.verify');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [AccountController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [AccountController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [AccountController::class, 'destroy'])->name('profile.destroy');
    Route::delete('/account', [AccountController::class, 'destroy'])->name('account.destroy');
    Route::get('/account', [ProfileController::class, 'show'])->name('account');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/destroy', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


//GOOGLE API AUTHENTICATION
Route::get('/auth/google', function () {
    return Socialite::driver('google')->redirect();
})->name('google.redirect');

Route::get('/auth/google/callback', function () {
    try {
        $googleUser = Socialite::driver('google')->user();
        
        //dd($googleUser); // Debug: Check if this returns user data

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'password' => bcrypt(str()->random(16)),
            ]
        );

        Auth::login($user);

        return redirect('/dashboard');
    } catch (\Exception $e) {
        dd($e->getMessage()); // Debug: Check for errors
    }
})->name('google.callback');



//ONLY VERIFIED USERS CAN ACCESS THE DASHBOARD/ real gmail verification
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Route to trigger email verification
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Handle email verification
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard')->with('success', 'Email verified successfully!');
})->middleware(['auth', 'signed'])->name('verification.verify');

// Resend email verification
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

require __DIR__.'/auth.php';

