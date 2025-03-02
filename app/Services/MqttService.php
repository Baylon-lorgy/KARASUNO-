<?php

namespace App\Services;

use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Models\WaterLevel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class MqttService
{
    protected $host;
    protected $port;
    protected $wsPort;
    protected $topic;
    protected $clientId;

    public function __construct()
    {
        $this->host = config('mqtt.host');
        $this->port = config('mqtt.port');
        $this->wsPort = config('mqtt.ws_port');
        $this->topic = config('mqtt.topic');
        $this->clientId = config('mqtt.client_id');
    }

    /**
     * Get WebSocket configuration for browser clients
     */
    public function getWebsocketConfig()
    {
        return [
            'host' => $this->host,
            'port' => $this->wsPort,  // Use WebSocket port
            'topic' => $this->topic,
            'clientId' => $this->clientId
        ];
    }

    /**
     * Store water level reading
     */
    public function storeReading(string $status): void
    {
        try {
            WaterLevel::create([
                'status' => $status,
                'timestamp' => Carbon::now()
            ]);
            Log::info('Water level reading stored', ['status' => $status]);
        } catch (\Exception $e) {
            Log::error('Error storing water level reading', [
                'error' => $e->getMessage(),
                'status' => $status
            ]);
        }
    }
} 