<?php

namespace Modules\Accounting\app\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminOnlyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->isVendor()) {
            abort(403, 'Access denied. Admin access required.');
        }

        return $next($request);
    }
}
