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
    }

    protected function checkEmailExists($email)
    {
        // Implement your logic to check if the email exists using a third-party service or API
        // For example, you can use an email verification API like Hunter, ZeroBounce, etc.
        // Return true if the email exists, otherwise return false.

        // Placeholder implementation, replace with actual API call
        return true;
    }
}