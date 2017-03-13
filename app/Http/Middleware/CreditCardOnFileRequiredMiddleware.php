<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Http\Middleware;

use Closure;
use Messages;

/**
 * Class Subscribed
 * @package Kabooodle\Http\Middleware
 */
class CreditCardOnFileRequiredMiddleware
{
    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $subscription
     * @return mixed
     */
    public function handle($request, Closure $next, $subscription)
    {
        if ($request->user() && ! $request->user()->hasCardOnFile()) {
            Messages::error('Credit card required.');

            return redirect()->route('profile.creditcard.index');
        }

        return $next($request);
    }
}
