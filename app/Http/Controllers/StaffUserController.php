<?php

namespace App\Http\Controllers;

use App\Models\StaffUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Mail\StaffInvitation;
use App\Mail\StaffApproved;

class StaffUserController extends Controller
{
    public function index(Request $request)
    {
        $query = StaffUser::query();

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff_users,email',
            'role' => 'required|in:staff,manager,admin'
        ]);

        $user = StaffUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'status' => 'pending',
            'permissions' => StaffUser::$rolePermissions[$request->role] ?? []
        ]);

        // Generate invitation token and send email
        $token = $user->generateInvitationToken();
        
        Mail::to($user->email)->send(new StaffInvitation($user));

        return response()->json([
            'message' => 'Staff invitation sent successfully',
            'user' => $user
        ]);
    }

    public function show($id)
    {
        $user = StaffUser::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = StaffUser::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff_users,email,' . $id,
            'role' => 'required|in:staff,manager,admin',
            'status' => 'sometimes|in:pending,active,inactive'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'permissions' => StaffUser::$rolePermissions[$request->role] ?? []
        ]);

        if ($request->filled('status')) {
            $user->update(['status' => $request->status]);
        }

        return response()->json([
            'message' => 'Staff user updated successfully',
            'user' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = StaffUser::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Staff user deleted successfully']);
    }

    public function approve($id)
    {
        $user = StaffUser::findOrFail($id);
        
        if (!$user->isPending()) {
            return response()->json(['error' => 'User is not pending approval'], 400);
        }

        $user->approve(Auth::user()->name ?? 'System');

        // Send approval notification
        Mail::to($user->email)->send(new StaffApproved($user));

        return response()->json([
            'message' => 'Staff user approved successfully',
            'user' => $user
        ]);
    }

    public function resendInvitation($id)
    {
        $user = StaffUser::findOrFail($id);
        
        if ($user->invitation_accepted_at) {
            return response()->json(['error' => 'Invitation already accepted'], 400);
        }

        $token = $user->generateInvitationToken();
        Mail::to($user->email)->send(new StaffInvitation($user));

        return response()->json(['message' => 'Invitation resent successfully']);
    }

    public function getRoles()
    {
        return response()->json([
            'staff' => 'Staff',
            'manager' => 'Manager', 
            'admin' => 'Admin'
        ]);
    }

    public function getPermissions()
    {
        return response()->json([
            'dashboard_view' => 'View Dashboard',
            'watering_control' => 'Watering Control',
            'sensor_history' => 'Sensor History',
            'user_management' => 'User Management',
            'audit_logs' => 'Audit Logs',
            'system_settings' => 'System Settings'
        ]);
    }
} 