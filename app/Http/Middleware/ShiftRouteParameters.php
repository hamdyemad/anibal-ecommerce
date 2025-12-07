<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ShiftRouteParameters
{
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        if ($route) {
            $parameters = $route->parameters();

            // Remove the first two parameters (lang and country)
            // so all controllers see only the “real” parameters
            $newParameters = array_slice($parameters, 2, null, true);

            foreach ($newParameters as $key => $value) {
                $route->setParameter($key, $value);
            }
        }

        return $next($request);
    }
}
