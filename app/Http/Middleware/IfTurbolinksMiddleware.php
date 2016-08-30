<?php

namespace Kabooodle\Http\Middleware;

use Closure;

/**
 * Class IfTurbolinksMiddleware
 * @package Kabooodle\Http\Middleware
 */
class IfTurbolinksMiddleware
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $response = $next($request);

        if (! $request->isMethod('post') && method_exists($response, 'header')) {
            return $response->header('Turbolinks-Location', $request->getRequestUri());
        }

        return $response;
    }
}
