<?php

return [
    'default' => env('BROADCAST_DRIVER', 'pusher'),

    'connections' => [
        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY', '4ec8cc2390a0852e67a0'),
            'secret' => env('PUSHER_APP_SECRET', '113b2b5f349d400c5590'),
            'app_id' => env('PUSHER_APP_ID', '1968176'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER', 'ap1'),
                'host' => env('PUSHER_HOST') ?: 'api-'.env('PUSHER_APP_CLUSTER', 'ap1').'.pusher.com',
                'port' => env('PUSHER_PORT', 443),
                'scheme' => env('PUSHER_SCHEME', 'https'),
                'encrypted' => true,
                'useTLS' => true,
            ],
            'client_options' => [],
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
        ],
        'log' => [
            'driver' => 'log',
        ],
        'null' => [
            'driver' => 'null',
        ],
    ],
]; 