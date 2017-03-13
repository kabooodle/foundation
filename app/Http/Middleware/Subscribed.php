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
        $subscription = explode('|', $subscription);
        if ($request->user() && ! $this->hasSubscriptionAccess($request->user(), $subscription)) {
            Messages::error('Subscription required.');

            return redirect()->route('profile.subscription.index');
        }

        return $next($request);
    }

    /**
     * @param       $user
     * @param array $subscriptions
     *
     * @return bool
     */
    public function hasSubscriptionAccess($user, array $subscriptions)
    {
        foreach ($subscriptions as $subscription) {
            if($user->hasSubscriptionAccess($subscription)) {
                return true;
            }
        }

        return false;
    }
}
