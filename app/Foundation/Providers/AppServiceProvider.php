<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Foundation\Providers;

use AltThree\Bus\Dispatcher;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Kabooodle\Libraries\Messages\Messages;
use Kabooodle\Libraries\Messages\MessagesInterface;
Use Kabooodle\Presenters\Paginators\DefaultPaginator;


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
        $dispatcher->mapUsing(function ($command) {
            return Dispatcher::simpleMapping($command, 'Kabooodle\Bus', 'Kabooodle\Bus\Handlers');
        });
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->registerMessages();
        $this->registerPaginationPresenter();
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
        Paginator::presenter(function($paginator) {
            return new DefaultPaginator($paginator);
        });
    }
}
