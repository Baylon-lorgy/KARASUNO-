<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Show the forgot password form.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle sending the reset password email.
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validate the email input
        $request->validate(['email' => 'required|email']);

        // Send reset link to the provided email
        $status = Password::sendResetLink($request->only('email'));

        // Return response based on success or failure
        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }
}
