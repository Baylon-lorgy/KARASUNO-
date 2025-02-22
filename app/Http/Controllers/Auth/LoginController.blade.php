<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth; // Make sure to include this

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // Validate the login request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Attempt login
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Regenerate session for security
            $request->session()->regenerate();

            // Flash success message
            return redirect()->route('dashboard')->with('success', 'Successfully logged in!');
        }

        // If login fails, flash error message
        return redirect()->back()->with('error', 'You have entered an invalid account. Please try again.');
    }
}
