<?php

namespace App\Http\Middleware;

use Closure;

/**
 * 前置中间件
 */
class BeforeMiddleware 
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
