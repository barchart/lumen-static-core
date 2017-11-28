<?php

namespace Barchart\LumenStatic;

use Laravel\Lumen\Application;

class LumenStatic extends Application
{
    public function __construct($basePath = null)
    {
        $basePath = $basePath ?: realpath(getcwd().'/../');

        parent::__construct($basePath);

        $this->aliases['Illuminate\Session\SessionManager'] = 'session';
        $this->availableBindings = array_merge($this->availableBindings, [
            'cookie' => 'registerCookieBindings',
            'Illuminate\Contracts\Cookie\Factory' => 'registerCookieBindings',
            'Illuminate\Contracts\Cookie\QueueingFactory' => 'registerCookieBindings',
            'mailer' => 'registerMailBindings',
            'Illuminate\Contracts\Mail\Mailer' => 'registerMailBindings',
            'session' => 'registerSessionBindings',
            'session.store' => 'registerSessionBindings',
            'Illuminate\Session\SessionManager' => 'registerSessionBindings',
        ]);

        $this->registerDotEnv();
        $this->registerRoutes();
    }

    public function getConfigurationPath($name = null)
    {
        $path = parent::getConfigurationPath($name);

        if (! $name) {
            $appConfigDir = $this->basePath('config').'/';

            if (! file_exists($appConfigDir) && file_exists($staticPath = __DIR__.'/../../../config/')) {
                return $staticPath;
            }
        } else {
            $appConfigPath = $this->basePath('config').'/'.$name.'.php';

            if (! file_exists($appConfigPath) && file_exists($staticPath = __DIR__.'/../../../config/'.$name.'.php')) {
                return $staticPath;
            }
        }

        return $path;
    }

    protected function registerDotEnv()
    {
        try {
            (new \Dotenv\Dotenv(base_path()))->load();
        } catch (\Dotenv\Exception\InvalidPathException $e) {
            //
        }
    }

    protected function registerRoutes()
    {
        $this->router->group(['middleware' => \Illuminate\Session\Middleware\StartSession::class, 'namespace' => 'App\Http\Controllers'], function ($app) {
            if (file_exists($customRoutes = getcwd().'/../routes.php')) {
                require $customRoutes;
            }

            $app->get('{route:.*}', ['uses' => 'Controller@get']);
        });
    }

    /**
     * Register container bindings for the application.
     *
     * @return void
     */
    protected function registerCookieBindings()
    {
        $this->singleton('cookie', function () {
            return $this->loadComponent('session', 'Illuminate\Cookie\CookieServiceProvider', 'cookie');
        });
    }

    /**
     * Register container bindings for the application.
     *
     * @return void
     */
    protected function registerMailBindings()
    {
        $this->singleton('mailer', function () {
            return $this->loadComponent('mail', 'Illuminate\Mail\MailServiceProvider', 'mailer');
        });
    }

    /**
     * Register container bindings for the application.
     *
     * @return void
     */
    protected function registerSessionBindings()
    {
        $this->singleton('session', function () {
            return $this->loadComponent('session', 'Illuminate\Session\SessionServiceProvider');
        });
        $this->singleton('session.store', function () {
            return $this->loadComponent('session', 'Illuminate\Session\SessionServiceProvider', 'session.store');
        });
    }
}