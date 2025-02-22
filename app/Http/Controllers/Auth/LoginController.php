<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/'],
            'password' => ['required'],
        ]);

        // Check if the email exists using a third-party service or API
        if (!$this->checkEmailExists($request->email)) {
            return redirect()->back()->with('gmail_error', 'Please use real gmail account.');
        }

        if (Auth::attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('dashboard');
        }

        return redirect()->back()->with('gmail_error', 'Please use real gmail account.');
        // Check if user exists in the database
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return redirect()->back()->with('error', 'This account is not yet registered. Please sign up.');
    }

    // Attempt login
    if (Auth::attempt($request->only('email', 'password'))) {
        return redirect()->route('dashboard'); // Change this to your dashboard route
    }

    return redirect()->back()->with('error', 'Invalid credentials. Please try again.');

    }

    protected function authenticated(Request $request, $user)
{
    if (!$user->hasVerifiedEmail()) {
        auth()->logout();
        return redirect('/login')->with('error', 'You must verify your email first.');
    }
}
}