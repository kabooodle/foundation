<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Foundation\Providers;

use URL;
use AltThree\Bus\Dispatcher;
use Kabooodle\Services\DateFactory;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Kabooodle\Libraries\Messages\Messages;
use Kabooodle\Libraries\Messages\MessagesInterface;
use Kabooodle\Presenters\Paginators\DefaultPaginator;

/**
 * Class AppServiceProvider
 * @package Kabooodle\Foundation\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * @param Dispatcher $dispatcher
     */
    public function boot(Dispatcher $dispatcher)
    {
        if ($this->app->environment() == 'production') {
            URL::forceSchema('https');

            // Added this because named routes, specifically API named routes, don't generate secure
            // routes for some reason.
            // TODO: Fix this.
            $this->app['request']->server->set('HTTPS', true);
        }

        $dispatcher->mapUsing(function ($command) {
            return Dispatcher::simpleMapping($command, 'Kabooodle\Bus', 'Kabooodle\Bus\Handlers');
        });

        $this->registerDateFactory();
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->registerBugsnag();
        $this->registerMessages();
//        $this->registerPaginationPresenter();
    }

    protected function registerMessages()
    {
        $this->app->singleton('messages', function ($app) {
            return (new Messages)->setSessionStore($app['session.store']);
        });

        $this->app->singleton(MessagesInterface::class, function () {
            return $this->app['messages'];
        });
    }

    public function registerPaginationPresenter()
    {
        Paginator::presenter(function ($paginator) {
            return new DefaultPaginator($paginator);
        });
    }

    public function registerBugsnag()
    {
        $this->app->alias('bugsnag.logger', \Illuminate\Contracts\Logging\Log::class);
        $this->app->alias('bugsnag.logger', \Psr\Log\LoggerInterface::class);
    }

    public function registerDateFactory()
    {
        return $this->app->singleton(DateFactory::class, function($app){
            $appTimezone = $app['config']->get('app.timezone');
            return new DateFactory($appTimezone, current_timezone());
        });
    }
}
