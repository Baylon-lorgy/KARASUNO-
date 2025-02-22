<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetRequest;

class PasswordResetController extends Controller
{
    public function sendResetRequest(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $adminEmail = "2201105765@student.buksu.edu.ph"; // Change this to your admin email

        Mail::to($adminEmail)->send(new PasswordResetRequest($request->email));

        return back()->with('status', 'Your password reset request has been sent to the admin.');
    }
}
