<?php

return [
    'apps' => [
        [
            'id' => env('PUSHER_APP_ID', 'local'),
            'name' => env('APP_NAME', 'Photobooth'),
            'key' => env('PUSHER_APP_KEY', 'local'),
            'secret' => env('PUSHER_APP_SECRET', 'local'),
            'capacity' => null,
            'enable_client_messages' => false,
            'enable_statistics' => false,
        ],
    ],

    'dashboard' => [ 'port' => env('LARAVEL_WEBSOCKETS_PORT', 6001) ],
    'port' => env('LARAVEL_WEBSOCKETS_PORT', 6001),

    'ssl' => [
        'local_cert' => null,
        'local_pk' => null,
        'passphrase' => null,
        'verify_peer' => false,
    ],
];

