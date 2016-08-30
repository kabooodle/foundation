<?php

namespace Kabooodle\Http\Middleware;

use Closure;

/**
 * Class Subscribed
 * @package Kabooodle\Http\Middleware
 */
class Subscribed
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $subscription
     * @return mixed
     */
    public function handle($request, Closure $next, $subscription)
    {
        if ($request->user() && ! $request->user()->subscribed($subscription)) {
            return redirect('billing');
        }

        return $next($request);
    }
}
