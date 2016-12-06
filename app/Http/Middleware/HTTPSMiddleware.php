<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Class HTTPSMiddleware
 */
class HTTPSMiddleware
{
    /**
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (! $this->isSecure() && env('APP_ENV') === 'production') {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }

    /**
     * @return bool
     */
    public function isSecure()
    {
        return getProtocol($includeBackSlashes = false) == 'http';
    }
}