<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Here you may configure the rate limiting for your application. You are
    | free to adjust these settings as needed. The default settings are
    | provided as examples of what you might want to use.
    |
    */

    'throttle' => [
        'web' => [
            'driver' => 'cache',
            'connection' => 'default',
            'key' => 'throttle_web',
            'limit' => 60,
            'decay_minutes' => 1,
        ],

        'api' => [
            'driver' => 'cache',
            'connection' => 'default',
            'key' => 'throttle_api',
            'limit' => 60,
            'decay_minutes' => 1,
        ],
    ],

]; 