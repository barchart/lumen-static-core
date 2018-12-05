<?php

namespace Barchart\LumenStatic;

use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function get(Request $request, $route = '')
    {
        $path = $request->path();

        if (ends_with($path, '/index')) {
            // Do not allow URL's that end in /index, as without it works.
            return redirect(env('APP_URL').'/'.ltrim(str_replace('/index', '', $path), '/'), 301);
        }

        return view($this->getViewName($path));
    }

    protected function getViewName($route)
    {
        $view = ($route === '/') ? 'pages.index' : 'pages.' . str_replace('/', '.', $route);

        // If view does not exist, try the index view if it has not been attempted already.
        if (! view()->exists($view)) {
            if (! ends_with($view, 'index')) {
                return $this->getViewName($route.'/index');
            } else {
                abort(404);
            }
        }

        return $view;
    }
}
