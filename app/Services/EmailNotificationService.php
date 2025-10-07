<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use App\Models\Notification;

class EmailNotificationService
{
    public function sendNotificationEmail(Notification $notification)
    {
        // Check if email notifications are enabled
        if (!$this->shouldSendEmail($notification)) {
            return false;
        }

        try {
            $subject = $this->generateEmailSubject($notification);
            $body = $this->generateEmailBody($notification);

            Mail::raw($body, function ($message) use ($subject) {
                $message->to(config('mail.from.address'))
                    ->subject($subject);
            });

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to send notification email: ' . $e->getMessage());
            return false;
        }
    }

    private function shouldSendEmail(Notification $notification)
    {
        // Check notification priority against user preferences
        $priority = $notification->priority;
        
        // Only send emails for medium priority and above
        $priorityLevels = ['low' => 1, 'medium' => 2, 'high' => 3, 'critical' => 4];
        $minPriority = 2; // medium
        
        return isset($priorityLevels[$priority]) && $priorityLevels[$priority] >= $minPriority;
    }

    private function generateEmailSubject(Notification $notification)
    {
        $priority = $notification->priority;
        $title = $notification->title;

        switch ($priority) {
            case 'critical':
                return "🚨 CRITICAL: {$title}";
            case 'high':
                return "⚠️ URGENT: {$title}";
            case 'medium':
                return "📢 {$title}";
            default:
                return "ℹ️ {$title}";
        }
    }

    private function generateEmailBody(Notification $notification)
    {
        $body = "RainBasin Pro Notification\n";
        $body .= "========================\n\n";
        $body .= "Priority: " . strtoupper($notification->priority) . "\n";
        $body .= "Time: " . $notification->created_at->format('Y-m-d H:i:s') . "\n\n";
        $body .= "Message:\n";
        $body .= $notification->message . "\n\n";
        $body .= "Type: " . $notification->type . "\n\n";
        $body .= "---\n";
        $body .= "This is an automated notification from your RainBasin Pro system.\n";
        $body .= "Please log in to your dashboard for more details.";

        return $body;
    }

    public function sendWateringAlert($action, $duration = null, $source = 'manual')
    {
        $subject = $action === 'started' ? 'Watering Started' : 'Watering Stopped';
        $message = $action === 'started' 
            ? "Watering has started for {$duration} minutes via {$source} control."
            : "Watering has stopped via {$source} control.";

        $notification = new Notification([
            'type' => 'watering_' . $action,
            'title' => $subject,
            'message' => $message,
            'priority' => 'low'
        ]);

        return $this->sendNotificationEmail($notification);
    }

    public function sendSensorAlert($sensorType, $value, $threshold, $severity = 'medium')
    {
        $subject = 'Sensor Alert';
        $message = "{$sensorType} sensor reading ({$value}) has exceeded the threshold ({$threshold}).";

        $notification = new Notification([
            'type' => 'sensor_threshold',
            'title' => $subject,
            'message' => $message,
            'priority' => $severity
        ]);

        return $this->sendNotificationEmail($notification);
    }

    public function sendSystemAlert($title, $message, $priority = 'medium')
    {
        $notification = new Notification([
            'type' => 'system_alert',
            'title' => $title,
            'message' => $message,
            'priority' => $priority
        ]);

        return $this->sendNotificationEmail($notification);
    }
} 