<?php

namespace App\Http\Middleware;

use Closure;

/**
 * 后置中间件
 */
class AfterMiddleware 
{
    public function handle($request, Closure $next)
    {
        $response =  $next($request);


        return $response;
    }
}
