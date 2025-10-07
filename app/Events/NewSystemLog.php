<?php

namespace App\Events;

use App\Models\SystemLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewSystemLog implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $log;

    public function __construct(SystemLog $log)
    {
        $this->log = $log;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('system-logs');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->log->id,
            'type' => $this->log->type,
            'status' => $this->log->status,
            'message' => $this->log->message,
            'deviceId' => $this->log->device_id,
            'details' => $this->log->details,
            'timestamp' => $this->log->created_at->toISOString(),
        ];
    }
} 