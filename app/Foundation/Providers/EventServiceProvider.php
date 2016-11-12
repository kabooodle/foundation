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

        // CLAIM EVENTS
        \Kabooodle\Bus\Events\Claim\NewItemWasClaimedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Claim\ItemWasClaimedEventHandler::class
        ],
        \Kabooodle\Bus\Events\Claim\ClaimWasRejectedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Claim\ClaimWasRejectedEventHandler::class
        ],
        \Kabooodle\Bus\Events\Claim\ClaimWasAcceptedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Claim\MoveClaimToShippingEventHandler::class
        ],

        // GROUP EVENTS
        \Kabooodle\Bus\Events\Group\GroupWasCreatedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Group\GroupWasCreatedEventHandler::class
        ],

        // INVENTORY EVENTS
        \Kabooodle\Bus\Events\Inventory\InventoryItemWasAddedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Inventory\InventoryItemWasAddedEventHandler::class
        ],
        \Kabooodle\Bus\Events\Inventory\InventoryItemWasRemovedFromSaleEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Inventory\InventoryItemWasRemovedFromSaleEventHandler::class
        ],
        \Kabooodle\Bus\Events\Comments\CommentWasCreatedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Comments\CommentWasCreatedEventHandler::class
        ],

        // PROFILE EVENTS
        \Kabooodle\Bus\Events\Profile\UserWasSubscribedToPlanEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Profile\UserWasSubscribedToPlanEventHandler::class
        ],

        // SHIPPING EVENTS
        \Kabooodle\Bus\Events\Shipping\ShippingTransactionWasCreatedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Shipping\MoveLabelToS3Handler::class,
            \Kabooodle\Bus\Handlers\Events\Shipping\DispatchShippingWebhookHandler::class
        ],
        \Kabooodle\Bus\Events\Shipping\ShippingLabelPrinted::class => [
            \Kabooodle\Bus\Handlers\Events\Shipping\ShippingLabelPrintedEventHandler::class
        ],

        // SOCIAL EVENTS
        \Kabooodle\Bus\Events\Social\UserFacebookCredentialsRevokedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Social\UserFacebookCredentialsRevokedEventHandler::class
        ],

        // USER EVENTS
        \Kabooodle\Bus\Events\User\UserWasCreatedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\User\UserWasCreatedListener::class,
        ],
        \Kabooodle\Bus\Events\User\UserLoggedInEvent::class => [
            \Kabooodle\Bus\Handlers\Events\User\UserLoggedInListener::class
        ],
    ];
    /**
     * @var array
     */
    protected $subscribe = [
        \Kabooodle\Bus\Handlers\Events\BroadcastEventListener::class
    ];
}
