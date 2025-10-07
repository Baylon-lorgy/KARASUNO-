<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\MaintenanceLog;
use App\Models\NotificationSetting;
use App\Models\AlertThreshold;
use App\Notifications\SystemAlert;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class SystemManagementController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/SystemManagement');
    }

    public function getSystemStatus()
    {
        try {
            // Check power status (example: check UPS status)
            $powerStatus = $this->checkPowerStatus();
            
            // Check sensor status
            $sensorStatus = $this->checkSensorStatus();
            
            // Check network status
            $networkStatus = $this->checkNetworkStatus();
            
            // Check storage status
            $storageStatus = $this->checkStorageStatus();

            return response()->json([
                'health' => [
                    'power' => $powerStatus,
                    'sensors' => $sensorStatus,
                    'network' => $networkStatus,
                    'storage' => $storageStatus
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting system status: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get system status'], 500);
        }
    }

    public function getMaintenanceLogs()
    {
        try {
            $logs = MaintenanceLog::orderBy('created_at', 'desc')
                ->take(10)
                ->get();

            return response()->json(['logs' => $logs]);
        } catch (\Exception $e) {
            Log::error('Error getting maintenance logs: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get maintenance logs'], 500);
        }
    }

    public function updateNotificationSettings(Request $request)
    {
        try {
            $user = auth()->user();
            $settings = $request->input('notifications');

            NotificationSetting::updateOrCreate(
                ['user_id' => $user->id],
                $settings
            );

            return response()->json(['message' => 'Notification settings updated successfully']);
        } catch (\Exception $e) {
            Log::error('Error updating notification settings: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update notification settings'], 500);
        }
    }

    public function updateAlertThresholds(Request $request)
    {
        try {
            $thresholds = $request->input('thresholds');

            foreach ($thresholds as $sensor => $values) {
                AlertThreshold::updateOrCreate(
                    ['sensor_type' => $sensor],
                    $values
                );
            }

            return response()->json(['message' => 'Alert thresholds updated successfully']);
        } catch (\Exception $e) {
            Log::error('Error updating alert thresholds: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update alert thresholds'], 500);
        }
    }

    public function restartSystem()
    {
        try {
            // Log the restart attempt
            MaintenanceLog::create([
                'message' => 'System restart initiated by user',
                'status' => 'success',
                'user_id' => auth()->id()
            ]);

            // Execute system restart command (example for Linux)
            exec('sudo systemctl restart rainwater-monitor');

            return response()->json(['message' => 'System restart initiated']);
        } catch (\Exception $e) {
            Log::error('Error restarting system: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to restart system'], 500);
        }
    }

    private function checkPowerStatus()
    {
        // Example: Check UPS status
        // This is a placeholder - implement actual power monitoring logic
        return 'normal';
    }

    private function checkSensorStatus()
    {
        // Example: Check if all sensors are responding
        // This is a placeholder - implement actual sensor checking logic
        return 'normal';
    }

    private function checkNetworkStatus()
    {
        // Example: Check network connectivity
        // This is a placeholder - implement actual network checking logic
        return 'normal';
    }

    private function checkStorageStatus()
    {
        // Example: Check storage space
        // This is a placeholder - implement actual storage checking logic
        return 'normal';
    }
} 