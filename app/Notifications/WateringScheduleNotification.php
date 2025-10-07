<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Notifications\Channels\MongoDbChannel;

class WateringScheduleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $schedule;
    protected $status;
    protected $duration;
    protected $message;

    public function __construct($schedule, $status, $duration, $message)
    {
        $this->schedule = $schedule;
        $this->status = $status;
        $this->duration = $duration;
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['mail', MongoDbChannel::class];
    }

    public function toMail($notifiable)
    {
        $statusColor = $this->status === 'FAILED' ? 'red' : 'green';
        
        return (new MailMessage)
            ->subject("Watering Schedule {$this->status}")
            ->greeting("Hello {$notifiable->name},")
            ->line($this->message)
            ->line("Schedule: {$this->schedule}")
            ->line("Duration: {$this->duration}")
            ->line("Status: {$this->status}")
            ->line("")
            ->when($this->status === 'FAILED', function ($mail) {
                return $mail->line('Recommendations:')
                    ->line('Manual watering REQUIRED.');
            })
            ->action('View Schedule', url('/admin/waterschedule'));
    }

    public function toArray($notifiable)
    {
        return [
            'schedule' => $this->schedule,
            'status' => $this->status,
            'duration' => $this->duration,
            'message' => $this->message,
            'type' => 'watering_schedule',
            'priority' => $this->status === 'FAILED' ? 'high' : 'normal'
        ];
    }
} 