<?php

namespace Barchart\LumenStatic;

use Laravel\Lumen\Application;

class LumenStatic extends Application
{
    public function __construct($basePath = null)
    {
        $basePath = $basePath ?: realpath(getcwd().'/../');

        parent::__construct($basePath);

        $this->availableBindings = array_merge($this->availableBindings, [
            'mailer' => 'registerMailBindings',
            'Illuminate\Contracts\Mail\Mailer' => 'registerMailBindings',
        ]);

        $this->registerDotEnv();
        $this->registerRoutes();
    }

    public function getConfigurationPath($name = null)
    {
        $path = parent::getConfigurationPath($name);

        if (! $path) {
            if (! $name) {
                if (file_exists($path = __DIR__.'/../../../config/')) {
                    return $path;
                }
            } else {
                if (file_exists($path = __DIR__.'/../../../config/'.$name.'.php')) {
                    return $path;
                }
            }
        }

        return $path;
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

    protected function registerMailBindings()
    {
        $this->singleton('mailer', function () {
            $this->configure('mail');

            return $this->loadComponent('mail', 'Illuminate\Mail\MailServiceProvider', 'mailer');
        });
    }
}