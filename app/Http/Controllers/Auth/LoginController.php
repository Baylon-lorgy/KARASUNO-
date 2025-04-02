<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Check if the email matches the admin email
        if ($credentials['email'] !== 'admin@buksu.edu.ph') {
            return back()->withErrors([
                'email' => 'Only admin users are allowed to access this system.',
            ]);
        }

        // Check if the password matches the admin password
        if ($credentials['password'] !== '49H4gNXGA5') {
            return back()->withErrors([
                'password' => 'Invalid password for admin account.',
            ]);
        }

        // Log in the admin user
        Auth::loginUsingId(1);
        $request->session()->regenerate();
        return redirect()->intended('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
} 