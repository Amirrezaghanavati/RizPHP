<?php

namespace System\Application;

class Application
{
    public function __construct()
    {
        $this->loadProviders();
        $this->loadHelpers();
        $this->registerRoutes();
        $this->routing();
    }

    private function loadProviders(): void
    {
        $appConfigs = require dirname(__DIR__, 2) . '/config/app.php';
        $providers = $appConfigs['providers'];
        foreach ($providers as $provider) {
            (new $provider())->boot();
        }
    }

    private function loadHelpers(): void
    {
        require_once(dirname(__DIR__) . '/helpers/helper.php');
        if (file_exists(dirname(__DIR__,2). '/app/Http/Helpers.php')){
            require_once(dirname(__DIR__,2). '/app/Http/Helpers.php');
        }
    }



    private function registerRoutes(): void
    {
        global $routes;
        $routes = [
            'get' => [],
            'post' => [],
            'put' => [],
            'delete' => []
        ];
        require_once(dirname(__DIR__,2) . '/routes/web.php');
        require_once(dirname(__DIR__,2) . '/routes/api.php');
    }

    private function routing(): void
    {
        (new \System\Router\Routing())->run();
    }

}