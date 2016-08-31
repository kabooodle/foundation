<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Foundation\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package Kabooodle\Foundation\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $listen = [
        \Kabooodle\Bus\Events\User\UserWasCreatedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\User\UserWasCreatedListener::class,
        ],
        \Kabooodle\Bus\Events\User\UserLoggedInEvent::class => [
            \Kabooodle\Bus\Handlers\Events\User\UserLoggedInListener::class
        ],
        \Kabooodle\Bus\Events\Group\GroupWasCreatedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Group\GroupWasCreatedEventHandler::class
        ],
        \Kabooodle\Bus\Events\Inventory\InventoryItemWasAddedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Inventory\InventoryItemWasAddedEventHandler::class
        ]
    ];
    /**
     * @var array
     */
    protected $subscribe = [
        \Kabooodle\Bus\Handlers\Events\BroadcastEventListener::class
    ];
}
