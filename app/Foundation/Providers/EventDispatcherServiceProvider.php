<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Foundation\Providers;

use Illuminate\Support\ServiceProvider;
use Kabooodle\Services\EventDispatcher;

/**
 * Class EventDispatcherServiceProvider
 * @package Kabooodle\Foundation\Providers
 */
class EventDispatcherServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('events', function ($app) {
            return (new EventDispatcher($app))->setQueueResolver(function () use ($app) {
                return $app->make('Illuminate\Contracts\Queue\Factory');
            });
        });
    }
}
