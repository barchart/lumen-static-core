<?php

if (! function_exists('old')) {
    /**
     * Retrieve an old input item.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function old($key = null, $default = null)
    {
        return app('request')->old($key, $default);
    }
}

if (! function_exists('csrf_token')) {
    /**
     * Get the CSRF token value.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    function csrf_token()
    {
        $session = app('session');
        if (isset($session)) {
            return $session->token();
        }
        throw new RuntimeException('Application session store not set.');
    }
}

if (! function_exists('app_url')) {
    /**
     * Generate a url for the application.
     *
     * @param  string  $path
     * @param  mixed   $parameters
     * @param  bool    $secure
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    function app_url($path = null, array $parameters = [])
    {
        $url = rtrim(env('APP_URL').'/'.ltrim($path, '/'), '/');

        if ($parameters) {
            $url .= '?'.http_build_query($parameters);
        }

        return $url;
    }
}

if (! function_exists('app_route')) {
    /**
     * Generate application URL based on route name.
     *
     * @param  string  $path
     * @param  mixed   $parameters
     * @return string
     */
    function app_route($name, $parameters = [])
    {
        return str_replace(app('request')->root(), env('APP_URL'), route($name, $parameters));
    }
}
