<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Http\Middleware;

use Closure;
use Messages;

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
            Messages::error('Subscription required.');

            return redirect()->route('profile.subscription.index');
        }

        return $next($request);
    }
}
