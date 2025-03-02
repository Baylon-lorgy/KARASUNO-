<?php

use Illuminate\Support\Str;

return [
    /*
    |--------------------------------------------------------------------------
    | MQTT Broker Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for connecting to the MQTT broker.
    |
    */

    'host' => env('MQTT_HOST', '192.168.1.10'),
    'port' => env('MQTT_PORT', 1883),
    'ws_port' => env('MQTT_WS_PORT', 9001),
    'username' => env('MQTT_USERNAME', ''),
    'password' => env('MQTT_PASSWORD', ''),
    'topic' => env('MQTT_TOPIC', 'water/level'),
    'client_id' => env('MQTT_CLIENT_ID', 'laravel_' . Str::random(4)),
    
    /*
    |--------------------------------------------------------------------------
    | MQTT Connection Settings
    |--------------------------------------------------------------------------
    |
    | Additional settings for the MQTT connection.
    |
    */

    'keep_alive' => 60,
    'protocol_level' => 4,
    'clean_session' => true,
]; 