<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WaterLevel;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\MqttClientException;

class SubscribeMQTT extends Command
{
    protected $signature = 'mqtt:subscribe';
    protected $description = 'Subscribe to MQTT and store water level data';
    
    private $shouldRun = true;
    private $reconnectDelay = 5; // seconds

    public function handle()
    {
        while ($this->shouldRun) {
            try {
                $this->connectAndSubscribe();
            } catch (\Exception $e) {
                $this->error("Error occurred: " . $e->getMessage());
                $this->info("Reconnecting in {$this->reconnectDelay} seconds...");
                sleep($this->reconnectDelay);
            }
        }
    }

    private function connectAndSubscribe()
    {
        $server = config('mqtt.host'); // Use config values
        $port = config('mqtt.port');
        $clientId = config('mqtt.client_id');

        // Create connection settings
        $connectionSettings = (new ConnectionSettings)
            ->setKeepAliveInterval(60)
            ->setLastWillTopic('sensor/water-level/status')
            ->setLastWillMessage('offline')
            ->setLastWillQualityOfService(1)
            ->setConnectTimeout(60);

        $mqtt = new MqttClient($server, $port, $clientId);

        $this->info('Connecting to MQTT Broker...');
        $mqtt->connect($connectionSettings, true);
        $this->info('Connected successfully!');

        // Set up disconnect handler
        $mqtt->registerLoopEventHandler(function (MqttClient $mqtt) {
            if (!$mqtt->isConnected()) {
                throw new \Exception('Connection lost. Reconnecting...');
            }
        });

        // Subscribe to topic
        $mqtt->subscribe(config('mqtt.topic'), function (string $topic, string $message) {
            $this->processMessage($topic, $message);
        }, 1);

        $this->info('Subscribed to ' . config('mqtt.topic'));
        
        // Start the event loop
        while ($this->shouldRun) {
            $mqtt->loop(true);
        }

        // Clean disconnect
        $mqtt->disconnect();
    }

    private function processMessage(string $topic, string $message)
    {
        try {
            $this->info("Received on {$topic}: {$message}");
            
            // Convert "Water Detected" message to a status value
            $level = $message === "Water Detected" ? 1 : 0;
            
            WaterLevel::create([
                'level' => $level,
                'status' => $message,
                'timestamp' => now(),
            ]);
            
            $this->info("Data stored successfully");
        } catch (\Exception $e) {
            $this->error("Failed to process message: " . $e->getMessage());
        }
    }

    public function __destruct()
    {
        $this->shouldRun = false;
    }
}