<?php

namespace App\Http\Controllers;

use App\Models\StaffUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\StaffInvitation;
use App\Mail\StaffApproved;
use Inertia\Inertia;

class InvitationController extends Controller
{
    public function index()
    {
        // Get all users with invitations sent, ordered by most recent
        $allInvitations = StaffUser::whereNotNull('invitation_sent_at')
            ->orderBy('invitation_sent_at', 'desc')
            ->get();

        // Separate pending invitations
        $pendingInvitations = $allInvitations->where('status', 'pending');

        // Return JSON if it's an AJAX request
        if (request()->wantsJson()) {
            return response()->json([
                'pendingInvitations' => $pendingInvitations->values(),
                'recentInvitations' => $allInvitations->take(10)->values()
            ]);
        }

        return Inertia::render('Admin/Invitations', [
            'pendingInvitations' => $pendingInvitations->values(),
            'recentInvitations' => $allInvitations->take(10)->values()
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/CreateInvitation');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff_users,email',
            'role' => 'required|in:staff,manager,admin',
            'message' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
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

            // Return JSON response for AJAX requests
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Staff invitation sent successfully',
                    'user' => $user
                ]);
            }

            return redirect()->route('admin.invitations.index')
                ->with('success', 'Invitation sent successfully to ' . $user->email);
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => 'Failed to send invitation: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Failed to send invitation: ' . $e->getMessage()]);
        }
    }

    public function resend($id)
    {
        $user = StaffUser::findOrFail($id);
        
        if ($user->invitation_accepted_at) {
            return response()->json(['error' => 'Invitation already accepted'], 400);
        }

        try {
            $token = $user->generateInvitationToken();
            Mail::to($user->email)->send(new StaffInvitation($user));

            return response()->json(['message' => 'Invitation resent successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to resend invitation: ' . $e->getMessage()], 500);
        }
    }

    public function cancel($id)
    {
        $user = StaffUser::findOrFail($id);
        
        if ($user->status === 'active') {
            return response()->json(['error' => 'Cannot cancel active user'], 400);
        }

        try {
            $user->delete();
            return response()->json(['message' => 'Invitation cancelled successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to cancel invitation: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = StaffUser::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff_users,email,' . $id,
            'role' => 'required|in:staff,manager,admin'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'permissions' => StaffUser::$rolePermissions[$request->role] ?? []
        ]);

        return response()->json([
            'message' => 'Staff user updated successfully',
            'user' => $user
        ]);
    }

    public function approve($id)
    {
        $user = StaffUser::findOrFail($id);
        
        if (!$user->isPending()) {
            return response()->json(['error' => 'User is not pending approval'], 400);
        }

        $user->approve(auth()->user()->name ?? 'System');

        // Send approval notification
        Mail::to($user->email)->send(new StaffApproved($user));

        return response()->json([
            'message' => 'Staff user approved successfully',
            'user' => $user
        ]);
    }

    public function deactivate($id)
    {
        $user = StaffUser::findOrFail($id);
        
        if (!$user->isActive()) {
            return response()->json(['error' => 'User is not active'], 400);
        }

        try {
            $user->deactivate();
            return response()->json(['message' => 'Staff user deactivated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to deactivate user: ' . $e->getMessage()], 500);
        }
    }

    public function reactivate($id)
    {
        $user = StaffUser::findOrFail($id);
        
        if ($user->isActive()) {
            return response()->json(['error' => 'User is already active'], 400);
        }

        if ($user->status === 'pending') {
            return response()->json(['error' => 'Cannot reactivate pending user. Please approve them first.'], 400);
        }

        try {
            $user->reactivate();
            return response()->json(['message' => 'Staff user reactivated successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to reactivate user: ' . $e->getMessage()], 500);
        }
    }

    public function preview($id)
    {
        $user = StaffUser::findOrFail($id);
        
        return Inertia::render('Admin/InvitationPreview', [
            'user' => $user,
            'invitationUrl' => $user->getInvitationUrl(),
            'rolePermissions' => $user->getRolePermissions()
        ]);
    }
} 