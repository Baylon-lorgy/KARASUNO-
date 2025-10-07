<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SensorThresholdAlert implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sensorId;
    public $sensorType;
    public $value;
    public $threshold;
    public $timestamp;
    public $severity;

    /**
     * Create a new event instance.
     */
    public function __construct($sensorId, $sensorType, $value, $threshold, $severity = 'medium')
    {
        $this->sensorId = $sensorId;
        $this->sensorType = $sensorType;
        $this->value = $value;
        $this->threshold = $threshold;
        $this->severity = $severity;
        $this->timestamp = now();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('sensor-alerts'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'sensorId' => $this->sensorId,
            'sensorType' => $this->sensorType,
            'value' => $this->value,
            'threshold' => $this->threshold,
            'severity' => $this->severity,
            'timestamp' => $this->timestamp
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'SensorThresholdAlert';
    }
} 