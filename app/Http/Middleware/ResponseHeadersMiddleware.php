<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Middleware;

use Closure;

class ResponseHeadersMiddleware
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
        
        $response->header('Expires', "Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        $response->header('Last-Modified', gmdate("D, d M Y H:i:s") . " GMT");
        $response->header('Cache-Control', "private, no-store, max-age=0, no-cache, must-revalidate, post-check=0, pre-check=0");
        $response->header('Pragma', "no-cache");

        return $response;
    }
}