<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            // Redirect to admin dashboard
            return redirect()->route('dashboard');
        }

        // Return back with an error message if login fails
        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }
}

