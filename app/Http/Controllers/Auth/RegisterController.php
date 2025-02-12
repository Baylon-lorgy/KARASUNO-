<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Check if the email exists using a third-party service or API
        if (!$this->checkEmailExists($request->email)) {
            return redirect()->back()->with('gmail_error', 'Please use a real gmail account.');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        auth()->login($user);

        return redirect()->route('dashboard');
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