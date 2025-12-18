<?php

return [
    'channels' => [
        'mail',
        'database',
    ],
    
    // Prefix for routes
    'route_prefix' => 'global-notification',
    
    // Middleware for admin routes
    'admin_middleware' => ['web'],
];
