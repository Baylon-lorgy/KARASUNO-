<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationEmail;
use App\Models\Notification;
use App\Models\User;

class NotificationController extends Controller
{
    public function sendEmail(Request $request)
    {
        $request->validate([
            'notification_id' => 'required|string',
            'urgency' => 'required|in:low,medium,high,critical',
            'type' => 'required|string',
            'title' => 'required|string',
            'message' => 'required|string',
            'priority' => 'required|in:low,medium,high,critical'
        ]);

        try {
            // Get all users who should receive email notifications
            $users = User::where('email_notifications', true)->get();
            
            if ($users->isEmpty()) {
                return response()->json(['message' => 'No users configured for email notifications'], 200);
            }

            $notificationData = [
                'id' => $request->notification_id,
                'urgency' => $request->urgency,
                'type' => $request->type,
                'title' => $request->title,
                'message' => $request->message,
                'priority' => $request->priority,
                'timestamp' => now()
            ];

            // Send email to each user
            foreach ($users as $user) {
                Mail::to($user->email)->send(new NotificationEmail($notificationData, $user));
            }

            // Log the email notification
            \Log::info('Email notification sent', [
                'notification_id' => $request->notification_id,
                'urgency' => $request->urgency,
                'recipients_count' => $users->count(),
                'type' => $request->type
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email notifications sent successfully',
                'recipients_count' => $users->count()
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to send email notification', [
                'error' => $e->getMessage(),
                'notification_id' => $request->notification_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send email notification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getNotificationSettings()
    {
        $user = auth()->user();
        
        return response()->json([
            'email_notifications' => $user->email_notifications ?? true,
            'notification_types' => [
                'system_alerts' => $user->system_alerts ?? true,
                'watering_events' => $user->watering_events ?? true,
                'sensor_alerts' => $user->sensor_alerts ?? true,
                'schedule_updates' => $user->schedule_updates ?? true,
                'security_alerts' => $user->security_alerts ?? true
            ]
        ]);
    }

    public function updateNotificationSettings(Request $request)
    {
        $request->validate([
            'email_notifications' => 'boolean',
            'system_alerts' => 'boolean',
            'watering_events' => 'boolean',
            'sensor_alerts' => 'boolean',
            'schedule_updates' => 'boolean',
            'security_alerts' => 'boolean'
        ]);

        $user = auth()->user();
        
        $user->update([
            'email_notifications' => $request->email_notifications,
            'system_alerts' => $request->system_alerts,
            'watering_events' => $request->watering_events,
            'sensor_alerts' => $request->sensor_alerts,
            'schedule_updates' => $request->schedule_updates,
            'security_alerts' => $request->security_alerts
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated successfully'
        ]);
    }
} 