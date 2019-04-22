<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection
    |--------------------------------------------------------------------------
    |
    | This option controls the name of the default connections that will be
    | used by your application.
    |
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Broadcastt Connections
    |--------------------------------------------------------------------------
    |
    | Here you may define each of the connections for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */
    'connections' => [

        'main' => [
            'id' => env('BROADCASTER_APP_ID'),
            'key' => env('BROADCASTER_APP_KEY'),
            'secret' => env('BROADCASTER_APP_SECRET'),
            'cluster' => env('BROADCASTER_APP_CLUSTER'),
        ],

        'alternative' => [
            'id' => 'your-alternative-app-id',
            'key' => 'your-alternative-app-key',
            'secret' => 'your-alternative-app-secret',
            'cluster' => 'your-alternative-app-cluster',
        ],

    ],
];
