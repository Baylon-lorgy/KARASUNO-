<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $notificationData;
    public $user;
    public $urgencyLevel;
    public $urgencyColor;
    public $urgencyIcon;

    /**
     * Create a new message instance.
     */
    public function __construct($notificationData, $user)
    {
        $this->notificationData = $notificationData;
        $this->user = $user;
        $this->urgencyLevel = $this->getUrgencyLevel($notificationData['urgency']);
        $this->urgencyColor = $this->getUrgencyColor($notificationData['urgency']);
        $this->urgencyIcon = $this->getUrgencyIcon($notificationData['type']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $urgency = $this->notificationData['urgency'];
        $subject = $this->getSubjectByUrgency($urgency);
        
        return new Envelope(
            subject: $subject,
            tags: ['notification', $urgency],
            metadata: [
                'notification_id' => $this->notificationData['id'],
                'urgency' => $urgency,
                'type' => $this->notificationData['type']
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.notification',
            with: [
                'notification' => $this->notificationData,
                'user' => $this->user,
                'urgencyLevel' => $this->urgencyLevel,
                'urgencyColor' => $this->urgencyColor,
                'urgencyIcon' => $this->urgencyIcon,
                'actionText' => $this->getActionText(),
                'footerText' => $this->getFooterText()
            ]
        );
    }

    private function getUrgencyLevel($urgency)
    {
        return match($urgency) {
            'critical' => 'CRITICAL ALERT',
            'high' => 'HIGH PRIORITY',
            'medium' => 'MEDIUM PRIORITY',
            'low' => 'INFORMATION',
            default => 'NOTIFICATION'
        };
    }

    private function getUrgencyColor($urgency)
    {
        return match($urgency) {
            'critical' => '#dc2626',
            'high' => '#ea580c',
            'medium' => '#2563eb',
            'low' => '#16a34a',
            default => '#6b7280'
        };
    }

    private function getUrgencyIcon($type)
    {
        return match($type) {
            'system_alert', 'security_alert' => '🚨',
            'sensor_threshold' => '⚠️',
            'watering_started' => '💧',
            'watering_stopped' => '⏹️',
            'schedule_created' => '📅',
            'schedule_deleted' => '🗑️',
            'user_activity' => '👤',
            'water_availability' => '✅',
            'system_info' => 'ℹ️',
            default => '🔔'
        };
    }

    private function getSubjectByUrgency($urgency)
    {
        $baseSubject = 'RainBasin Pro Notification';
        
        return match($urgency) {
            'critical' => "🚨 URGENT: {$baseSubject} - Immediate Action Required",
            'high' => "⚠️ HIGH PRIORITY: {$baseSubject}",
            'medium' => "📢 {$baseSubject}",
            'low' => "ℹ️ {$baseSubject}",
            default => $baseSubject
        };
    }

    private function getActionText()
    {
        $urgency = $this->notificationData['urgency'];
        $type = $this->notificationData['type'];
        
        if ($urgency === 'critical') {
            return 'IMMEDIATE ACTION REQUIRED - Please check your RainBasin Pro dashboard immediately.';
        }
        
        if ($urgency === 'high') {
            return 'Please review this notification and take appropriate action when convenient.';
        }
        
        if ($urgency === 'medium') {
            return 'This notification requires your attention. Please review when possible.';
        }
        
        return 'This is an informational notification. No immediate action required.';
    }

    private function getFooterText()
    {
        $urgency = $this->notificationData['urgency'];
        
        return match($urgency) {
            'critical' => 'This is a critical system alert that requires immediate attention.',
            'high' => 'This is a high-priority notification that should be addressed soon.',
            'medium' => 'This notification contains important information about your system.',
            'low' => 'This is an informational update about your RainBasin Pro system.',
            default => 'Thank you for using RainBasin Pro.'
        };
    }
} 