<?php

namespace Barchart\LumenStatic;

use Laravel\Lumen\Application;

class LumenStatic extends Application
{
    public function __construct($basePath = null)
    {
        $basePath = $basePath ?: realpath(getcwd().'/../');

        parent::__construct($basePath);

        $this->registerDotEnv();
        $this->registerRoutes();
    }

    protected function registerDotEnv()
    {
        try {
            (new \Dotenv\Dotenv(getcwd().'/../'))->load();
        } catch (\Dotenv\Exception\InvalidPathException $e) {
            //
        }
    }

    protected function registerRoutes()
    {
        $this->group(['namespace' => 'App\Http\Controllers'], function ($app) {
            if (file_exists($customRoutes = getcwd().'/../routes.php')) {
                require $customRoutes;
            }

            $app->get('{route:.*}', ['uses' => 'Controller@get']);
        });
    }
}