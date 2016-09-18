<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Middleware;

use Auth;
use Closure;

/**
 * Class ReferralProgramMiddleware
 * @package Kabooodle\Http\Middleware
 */
class ReferralProgramMiddleware
{
    const SESSION_KEY = 'kabooodle_referrer';
    const REQUEST_KEY = 'referred_by';

    /**
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @param  string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $response = $next($request);

        if (Auth::guard($guard)->guest()
            && $request->has(self::REQUEST_KEY)
            && !$this->sessionValueExists($request)
        ) {
            $this->setSessionValue($request);
        }

        return $response;
    }

    /**
     * @param  \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public function sessionValueExists($request)
    {
        return (bool) $request->session()->has(self::SESSION_KEY) && $request->session()->get(self::SESSION_KEY) == $request->get(self::REQUEST_KEY);
    }

    /**
     * @param  \Illuminate\Http\Request $request
     *
     * @return void
     */
    public function setSessionValue($request)
    {
        $request->session()->put(self::SESSION_KEY, $request->get(self::REQUEST_KEY));
    }
}