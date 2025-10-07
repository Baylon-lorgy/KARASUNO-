<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('web');
    }

    public function showLoginForm()
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => false,
            'status' => session('status'),
            'csrf_token' => csrf_token(),
        ]);
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

        // Find or create the admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@buksu.edu.ph'],
            [
                'name' => 'Admin',
                'password' => Hash::make('49H4gNXGA5'),
            ]
        );

        // Delete existing tokens and generate new API token
        $admin->tokens()->delete();
        $token = $admin->createToken('auth-token')->plainTextToken;
        
        // Store token in session
        $request->session()->put('auth_token', $token);

        // Log in the admin user
        Auth::login($admin);
        $request->session()->regenerate();
        
        // Return JSON response with token for API requests
        if ($request->wantsJson()) {
            return response()->json([
                'token' => $token,
                'user' => $admin,
            ]);
        }
        
        // Redirect to admin dashboard with token
        return redirect()->intended(route('admin.dashboard'))->with([
            'auth_token' => $token,
            'message' => 'Logged in successfully'
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke all tokens
        if ($user = $request->user()) {
            $user->tokens()->delete();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Logged out successfully']);
        }

        return redirect('/');
    }
} 