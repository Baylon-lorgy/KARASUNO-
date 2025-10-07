<?php

namespace App\Http\Controllers;

use App\Models\StaffUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class StaffAuthController extends Controller
{
    public function showLoginForm()
    {
        return Inertia::render('Staff/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $staffUser = StaffUser::where('email', $request->email)->first();

        if (!$staffUser) {
            return back()->withErrors([
                'email' => 'No staff account found with this email address.',
            ]);
        }

        if ($staffUser->status !== 'active') {
            return back()->withErrors([
                'email' => 'Your account is not yet approved. Please contact your administrator.',
            ]);
        }

        if (!Hash::check($request->password, $staffUser->password)) {
            return back()->withErrors([
                'password' => 'The provided password is incorrect.',
            ]);
        }

        // Log in the staff user using the 'staff' guard
        Auth::guard('staff')->login($staffUser);

        return redirect()->intended('/staff/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/staff/login');
    }

    public function dashboard()
    {
        $staffUser = Auth::guard('staff')->user();

        if (!$staffUser) {
            return redirect('/staff/login');
        }

        return Inertia::render('Staff/Dashboard', [
            'auth' => [
                'staffUser' => [
                    'id' => $staffUser->id,
                    'name' => $staffUser->name,
                    'email' => $staffUser->email,
                    'role' => $staffUser->role,
                    'status' => $staffUser->status,
                    'permissions' => $staffUser->getRolePermissions(),
                ],
            ]
        ]);
    }

    public function getDashboardData()
    {
        $staffUser = Auth::guard('staff')->user();

        if (!$staffUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get latest sensor data
        $sensorData = \App\Models\SensorData::latest()->first();

        // Get watering status
        $wateringStatus = 'idle'; // This would come from your watering system

        return response()->json([
            'sensorData' => $sensorData ? [
                'waterLevel' => $sensorData->water_level ?? 0,
                'temperature' => $sensorData->temperature ?? 0,
                'humidity' => $sensorData->humidity ?? 0,
            ] : null,
            'wateringStatus' => $wateringStatus,
        ]);
    }

    public function wateringControl(Request $request)
    {
        $staffUser = Auth::guard('staff')->user();

        if (!$staffUser || !$staffUser->hasPermission('watering_control')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'action' => 'required|in:start,stop',
        ]);

        // Here you would implement the actual watering control logic
        // For now, we'll just return a success response
        $status = $request->action === 'start' ? 'active' : 'idle';

        return response()->json([
            'status' => $status,
            'message' => 'Watering system ' . $request->action . 'ed successfully.',
        ]);
    }

    public function getSensorHistory(Request $request)
    {
        $staffUser = Auth::guard('staff')->user();

        if (!$staffUser) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $range = $request->get('range', '7d');
        
        // Get sensor data based on range
        $sensorData = \App\Models\SensorData::latest()
            ->when($range === '1d', function($query) {
                return $query->where('created_at', '>=', now()->subDay());
            })
            ->when($range === '7d', function($query) {
                return $query->where('created_at', '>=', now()->subDays(7));
            })
            ->when($range === '30d', function($query) {
                return $query->where('created_at', '>=', now()->subDays(30));
            })
            ->when($range === '90d', function($query) {
                return $query->where('created_at', '>=', now()->subDays(90));
            })
            ->take(100)
            ->get()
            ->map(function($data) {
                return [
                    'timestamp' => $data->created_at,
                    'waterLevel' => $data->water_level ?? 0,
                    'temperature' => $data->temperature ?? 0,
                    'humidity' => $data->humidity ?? 0,
                ];
            });

        return response()->json([
            'sensorData' => $sensorData,
        ]);
    }

    public function getUsers(Request $request)
    {
        $staffUser = Auth::guard('staff')->user();

        if (!$staffUser || !$staffUser->hasPermission('user_management')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $users = \App\Models\StaffUser::all()->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
            ];
        });

        return response()->json([
            'users' => $users,
        ]);
    }

    public function getAuditLogs(Request $request)
    {
        $staffUser = Auth::guard('staff')->user();

        if (!$staffUser || !$staffUser->hasPermission('audit_logs')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $range = $request->get('range', '7d');
        $type = $request->get('type', 'all');
        
        // Get audit logs based on range and type
        $logs = \App\Models\SystemLog::latest()
            ->when($range === '1d', function($query) {
                return $query->where('created_at', '>=', now()->subDay());
            })
            ->when($range === '7d', function($query) {
                return $query->where('created_at', '>=', now()->subDays(7));
            })
            ->when($range === '30d', function($query) {
                return $query->where('created_at', '>=', now()->subDays(30));
            })
            ->when($range === '90d', function($query) {
                return $query->where('created_at', '>=', now()->subDays(90));
            })
            ->when($type !== 'all', function($query) use ($type) {
                return $query->where('type', $type);
            })
            ->take(50)
            ->get()
            ->map(function($log) {
                return [
                    'title' => $log->title ?? 'System Event',
                    'message' => $log->message ?? 'No message',
                    'type' => $log->type ?? 'info',
                    'timestamp' => $log->created_at,
                    'user' => $log->user_name ?? null,
                    'ip' => $log->ip_address ?? null,
                ];
            });

        return response()->json([
            'logs' => $logs,
        ]);
    }

    public function waterControl()
    {
        $staffUser = Auth::guard('staff')->user();
        return Inertia::render('Staff/WaterControl', [
            'auth' => [
                'staffUser' => [
                    'id' => $staffUser->id,
                    'name' => $staffUser->name,
                    'email' => $staffUser->email,
                    'role' => $staffUser->role,
                    'status' => $staffUser->status,
                    'permissions' => $staffUser->getRolePermissions(),
                ],
            ]
        ]);
    }

    public function sensorHistory()
    {
        $staffUser = Auth::guard('staff')->user();
        return Inertia::render('Staff/SensorHistory', [
            'auth' => [
                'staffUser' => [
                    'id' => $staffUser->id,
                    'name' => $staffUser->name,
                    'email' => $staffUser->email,
                    'role' => $staffUser->role,
                    'status' => $staffUser->status,
                    'permissions' => $staffUser->getRolePermissions(),
                ],
            ]
        ]);
    }

    public function userManagement()
    {
        $staffUser = Auth::guard('staff')->user();
        return Inertia::render('Staff/UserManagement', [
            'auth' => [
                'staffUser' => [
                    'id' => $staffUser->id,
                    'name' => $staffUser->name,
                    'email' => $staffUser->email,
                    'role' => $staffUser->role,
                    'status' => $staffUser->status,
                    'permissions' => $staffUser->getRolePermissions(),
                ],
            ]
        ]);
    }

    public function auditLogs()
    {
        $staffUser = Auth::guard('staff')->user();
        return Inertia::render('Staff/AuditLogs', [
            'auth' => [
                'staffUser' => [
                    'id' => $staffUser->id,
                    'name' => $staffUser->name,
                    'email' => $staffUser->email,
                    'role' => $staffUser->role,
                    'status' => $staffUser->status,
                    'permissions' => $staffUser->getRolePermissions(),
                ],
            ]
        ]);
    }
} 