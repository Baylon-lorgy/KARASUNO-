<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WateringCommandSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $duration;

    public function __construct($duration)
    {
        $this->duration = $duration;
    }

    public function broadcastOn()
    {
        return new Channel('watering');
    }

    public function broadcastAs()
    {
        return 'WateringCommandSent';
    }
} 