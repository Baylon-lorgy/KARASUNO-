<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SensorDataUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $water_level;
    public $soil_moisture;

    /**
     * Create a new event instance.
     */
    public function __construct($water_level, $soil_moisture)
    {
        $this->water_level = $water_level;
        $this->soil_moisture = $soil_moisture;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [new Channel('sensor-data')];
    }

    public function broadcastAs(): string
    {
        return 'SensorDataUpdated';
    }

    public function broadcastWith(): array
    {
        return [
            'water_level' => $this->water_level,
            'soil_moisture' => $this->soil_moisture,
            'timestamp' => now()->toIso8601String()
        ];
    }
} 