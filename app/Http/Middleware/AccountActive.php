<?php

namespace Kabooodle\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Class Accountactive
 * @package Kabooodle\Http\Middleware
 */
class AccountActive
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest() || ! Auth::guard($guard)->user()->accountActive() ) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest(route('auth.login'));
            }
        }

        return $next($request);
    }
}
