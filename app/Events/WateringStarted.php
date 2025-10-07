<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WateringStarted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $zoneId;
    public $timestamp;
    public $duration;
    public $source;

    /**
     * Create a new event instance.
     */
    public function __construct($zoneId, $timestamp, $duration = null, $source = 'manual')
    {
        $this->zoneId = $zoneId;
        $this->timestamp = $timestamp;
        $this->duration = $duration;
        $this->source = $source;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('watering-events'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'zoneId' => $this->zoneId,
            'timestamp' => $this->timestamp,
            'duration' => $this->duration,
            'source' => $this->source,
            'action' => 'started'
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'WateringStarted';
    }
} 