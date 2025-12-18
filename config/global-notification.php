<?php

return [
    'channels' => [
        'mail',
        'database',
    ],

    // Prefix for routes
    'route_prefix' => 'global-notification',

    // Middleware for admin routes
    'admin_middleware' => env('GLOBAL_NOTIFICATION_AUTH_ENABLED', true) ? ['web', 'auth'] : ['web'],
];
