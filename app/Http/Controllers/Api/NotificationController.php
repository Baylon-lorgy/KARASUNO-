<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Events\NewNotification as NewNotificationEvent;
use App\Services\EmailNotificationService;

class NotificationController extends Controller
{
    protected $emailService;

    public function __construct(EmailNotificationService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function index(Request $request)
    {
        $notifications = Notification::orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return response()->json($notifications);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'title' => 'nullable|string',
            'message' => 'required|string',
            'priority' => 'nullable|in:low,medium,high,critical',
            'data' => 'nullable|array'
        ]);

        try {
            // Generate title from type if not provided
            $title = $request->title ?? $this->generateTitleFromType($request->type);
            
            // Set default priority if not provided
            $priority = $request->priority ?? $this->getDefaultPriority($request->type);

            $notification = Notification::create([
                'type' => $request->type,
                'title' => $title,
                'message' => $request->message,
                'priority' => $priority,
                'data' => $request->data ?? [],
                'read' => false,
                'created_at' => now()
            ]);

            // Broadcast the new notification event
            broadcast(new NewNotificationEvent($notification))->toOthers();

            // Send email notification if priority is medium or higher
            if (in_array($priority, ['medium', 'high', 'critical'])) {
                $this->emailService->sendNotificationEmail($notification);
            }

            return response()->json([
                'message' => 'Notification created successfully',
                'notification' => $notification
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to create notification: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateTitleFromType($type)
    {
        $titles = [
            'watering_started' => 'Watering Started',
            'watering_stopped' => 'Watering Stopped',
            'watering_schedule_started' => 'Watering Schedule Started',
            'watering_schedule_stopped' => 'Watering Schedule Stopped',
            'schedule_created' => 'Schedule Created',
            'schedule_modified' => 'Schedule Modified',
            'schedule_deleted' => 'Schedule Deleted',
            'water_level_low' => 'Water Level Low',
            'water_level_critical' => 'Water Level Critical',
            'sensor_offline' => 'Sensor Offline',
            'sensor_online' => 'Sensor Online',
            'maintenance_due' => 'Maintenance Due',
            'maintenance_completed' => 'Maintenance Completed',
            'weather_alert' => 'Weather Alert',
            'power_outage' => 'Power Outage',
            'backup_completed' => 'Backup Completed',
            'backup_failed' => 'Backup Failed',
            'update_available' => 'System Update Available',
            'update_installed' => 'System Update Installed',
            'data_export_completed' => 'Data Export Completed',
            'data_export_failed' => 'Data Export Failed',
            'audit_log_alert' => 'Security Alert',
            'compliance_report_due' => 'Compliance Report Due',
            'compliance_report_generated' => 'Compliance Report Generated',
            'system_alert' => 'System Alert',
            'security_alert' => 'Security Alert',
            'sensor_threshold' => 'Sensor Threshold Alert',
            'water_availability' => 'Water Availability',
            'user_activity' => 'User Activity',
            'system_info' => 'System Information',
            'staff_invitation_pending' => 'Staff Invitation Pending',
            'staff_invitation_approved' => 'Staff Invitation Approved',
            'staff_invitation_rejected' => 'Staff Invitation Rejected',
            'staff_account_activated' => 'Staff Account Activated',
            'staff_account_deactivated' => 'Staff Account Deactivated'
        ];
        
        return $titles[$type] ?? ucwords(str_replace('_', ' ', $type));
    }

    private function getDefaultPriority($type)
    {
        $priorities = [
            'watering_started' => 'low',
            'watering_stopped' => 'low',
            'watering_schedule_started' => 'low',
            'watering_schedule_stopped' => 'medium',
            'schedule_created' => 'low',
            'schedule_modified' => 'medium',
            'schedule_deleted' => 'medium',
            'water_level_low' => 'medium',
            'water_level_critical' => 'high',
            'sensor_offline' => 'high',
            'sensor_online' => 'low',
            'maintenance_due' => 'medium',
            'maintenance_completed' => 'low',
            'weather_alert' => 'medium',
            'power_outage' => 'critical',
            'backup_completed' => 'low',
            'backup_failed' => 'high',
            'update_available' => 'low',
            'update_installed' => 'low',
            'data_export_completed' => 'low',
            'data_export_failed' => 'medium',
            'audit_log_alert' => 'critical',
            'compliance_report_due' => 'medium',
            'compliance_report_generated' => 'low',
            'system_alert' => 'high',
            'security_alert' => 'critical',
            'sensor_threshold' => 'medium',
            'water_availability' => 'medium',
            'user_activity' => 'low',
            'system_info' => 'low',
            'staff_invitation_pending' => 'medium',
            'staff_invitation_approved' => 'low',
            'staff_invitation_rejected' => 'medium',
            'staff_account_activated' => 'low',
            'staff_account_deactivated' => 'high'
        ];
        
        return $priorities[$type] ?? 'medium';
    }

    public function markAsRead($id)
    {
        try {
            $notification = Notification::findOrFail($id);
            $notification->read = true;
            $notification->save();

            return response()->json([
                'message' => 'Notification marked as read',
                'notification' => $notification
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to mark notification as read: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markAllAsRead()
    {
        try {
            Notification::where('read', false)->update(['read' => true]);

            return response()->json([
                'message' => 'All notifications marked as read'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to mark all notifications as read: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $notification = Notification::findOrFail($id);
            $notification->delete();

            return response()->json([
                'message' => 'Notification deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to delete notification: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getNotificationSettings()
    {
        try {
            // For now, return default settings
            // In a real app, you'd fetch from user preferences
            $settings = [
                'email_notifications' => true,
                'system_alerts' => true,
                'watering_events' => true,
                'sensor_alerts' => true,
                'schedule_updates' => true,
                'security_alerts' => true,
                'email_priority' => 'medium' // low, medium, high, critical
            ];

            return response()->json($settings);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch notification settings: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateNotificationSettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'email_notifications' => 'boolean',
                'system_alerts' => 'boolean',
                'watering_events' => 'boolean',
                'sensor_alerts' => 'boolean',
                'schedule_updates' => 'boolean',
                'security_alerts' => 'boolean',
                'email_priority' => 'string|in:low,medium,high,critical'
            ]);

            // In a real app, you'd save these to user preferences
            // For now, we'll just return success
            return response()->json([
                'message' => 'Notification settings updated successfully',
                'settings' => $validated
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to update notification settings: ' . $e->getMessage()
            ], 500);
        }
    }
}
