<?php

return [
    'host' => env('WILDDUCK_HOST', 'http://localhost:8080'),
    'debug' => env('WILDDUCK_DEBUG', false),
    'access_token' => env('WILDDUCK_ACCESS_TOKEN', null),
    'request_timeout' => env('WILDDUCK_REQUEST_TIMEOUT', 10.0),
    'session' => [
        'use_cookie' => env('WILDDUCK_SESSION_USE_COOKIE', false),
        'name' => env('WILDDUCK_SESSION_NAME', 'webmail'),
    ],
];
