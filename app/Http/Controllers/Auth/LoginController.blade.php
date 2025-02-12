<?php
// filepath: /C:/Users/USER/Desktop/IOT Raincatch Basin/System/IotSytem/app/Http/Controllers/Auth/LoginController.php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // ... existing login logic ...

        if (auth()->attempt($credentials)) {
            // Set the flash message
            session()->flash('success', 'Successfully logged in!');
            return redirect()->route('dashboard');
        }

        // ... existing login logic ...
    }
}