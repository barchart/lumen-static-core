<?php

namespace Barchart\LumenStatic;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function get($route)
    {
        $view = $this->getViewName($route);

        if (! view()->exists($view)) {
            abort(404);
        }

        return view($view);
    }

    protected function getViewName($route)
    {
        // TODO: set template based on website being delivered.
        if (empty($route)) {
            return 'pages.home';
        }

        return 'pages.' . str_replace('/', '.', $route);
    }
}
