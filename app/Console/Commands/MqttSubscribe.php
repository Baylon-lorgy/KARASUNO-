<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use App\Models\WaterLevel;

class MqttSubscribe extends Command
{
    protected $signature = 'mqtt:subscribe';
    protected $description = 'Subscribe to MQTT topic and save messages to MongoDB';

    public function handle()
    {
        $server   = '192.168.1.10'; // Your Mosquitto MQTT broker IP
        $port     = 1883; // Default MQTT port
        $clientId = 'laravel_subscriber';

        $mqtt = new MqttClient($server, $port, $clientId);
        $mqtt->connect(null, true);

        // Subscribe to the topic
        $mqtt->subscribe('test/topic', function ($topic, $message) {
            echo "Received message on {$topic}: {$message}\n";

            // Store message in MongoDB
            WaterLevel::create([
                'status'    => $message,
                'timestamp' => now(),
            ]);
        }, 0);

        // Keep listening for messages
        $mqtt->loop(true);
    }
}