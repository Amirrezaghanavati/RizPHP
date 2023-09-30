<?php

return [
    'APP_TITLE' => 'RizPHP',
    'BASE_URL' => 'http://localhost:8000',
    'BASE_DIR' => dirname(__DIR__),
    'providers' => [
        \App\Providers\SessionProvider::class,
        \App\Providers\AppServiceProvider::class,
    ]
];