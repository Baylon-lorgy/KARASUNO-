<?php

namespace App\Http\Controllers;

use App\Models\StaffUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class StaffInvitationController extends Controller
{
    public function show($token)
    {
        $staffUser = StaffUser::where('invitation_token', $token)->first();
        
        if (!$staffUser) {
            return redirect('/')->with('error', 'Invalid or expired invitation link.');
        }
        
        if ($staffUser->invitation_accepted_at) {
            return redirect('/')->with('error', 'This invitation has already been accepted.');
        }
        
        return Inertia::render('Staff/Invitation', [
            'staffUser' => $staffUser,
            'token' => $token
        ]);
    }
    
    public function accept(Request $request, $token)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);
        
        $staffUser = StaffUser::where('invitation_token', $token)->first();
        
        if (!$staffUser) {
            return back()->withErrors(['error' => 'Invalid or expired invitation link.']);
        }
        
        if ($staffUser->invitation_accepted_at) {
            return back()->withErrors(['error' => 'This invitation has already been accepted.']);
        }
        
        $staffUser->acceptInvitation($request->password);
        
        return redirect('/login')->with('success', 'Account setup completed! Please wait for manager approval before logging in.');
    }
} 