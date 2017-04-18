<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Foundation\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Kabooodle\Bus\Handlers\Events\Claim\TrackAcceptedClaim;
use Kabooodle\Bus\Handlers\Events\User\AddNewUserToAllNotificationTypes;

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
            \Kabooodle\Bus\Handlers\Events\Claim\ItemWasClaimedEventHandler::class,
            \Kabooodle\Bus\Handlers\Events\Claim\SendNewClaimToKeen::class,
        ],
        \Kabooodle\Bus\Events\Claim\ClaimWasRejectedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Claim\ClaimWasRejectedEventHandler::class
        ],
        \Kabooodle\Bus\Events\Claim\ClaimWasCanceledEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Claim\ClaimWasCanceledEventHandler::class
        ],
        \Kabooodle\Bus\Events\Claim\ClaimWasAcceptedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Claim\TrackAcceptedClaim::class,
            \Kabooodle\Bus\Handlers\Events\Claim\MoveClaimToShippingEventHandler::class,
//            \Kabooodle\Bus\Handlers\Events\Claim\NotifyClaimWasAccepted::class,
        ],
        \Kabooodle\Bus\Events\Claim\NewGuestClaimEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Claim\GuestClaimedEventHandler::class
        ],



        // FLASHSALE EVENTS
        \Kabooodle\Bus\Events\Flashsale\FlashsaleWasCreatedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Flashsales\NotifySellersFlashsaleWasCreated::class,
            \Kabooodle\Bus\Handlers\Events\Flashsales\NotifyAdminsFlashsaleWasCreated::class,
        ],
        \Kabooodle\Bus\Events\Flashsale\FlashsaleWasUpdatedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Flashsales\HandleFlashsaleChangeOfAdminAndSellers::class
        ],


        // INVENTORY EVENTS
        \Kabooodle\Bus\Events\Inventory\InventoryItemWasAddedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Inventory\InventoryItemWasAddedEventHandler::class
        ],
        \Kabooodle\Bus\Events\Inventory\InventoryItemWasRemovedFromSaleEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Inventory\InventoryItemWasRemovedFromSaleEventHandler::class
        ],
        \Kabooodle\Bus\Events\Inventory\InventoryQuantityUpdatedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Inventory\InventoryQuantityUpdatedHandler::class
        ],
        \Kabooodle\Bus\Events\Comments\CommentWasCreatedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Comments\CommentWasCreatedEventHandler::class
        ],

        // PHONE NUMBERS
        \Kabooodle\Bus\Events\PhoneNumbers\PhoneNumberWasVerifiedSuccessfullyEvent::class => [
            \Kabooodle\Bus\Handlers\Events\PhoneNumbers\NotifyNewPhoneNumberVerified::class
        ],

        // MESSENGER
        \Kabooodle\Bus\Events\Messenger\MessageWasAddedToThreadEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Messenger\NotifyNewMessageForThreadCreated::class
        ],
        \Kabooodle\Bus\Events\Messenger\ThreadWasCreatedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Messenger\NotifyNewThreadWasCreatedEvent::class
        ],

        // LISTINGS
        \Kabooodle\Bus\Events\Listings\ListingScheduledEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Listings\NotifyListingWasScheduled::class,
            \Kabooodle\Bus\Handlers\Events\Listings\SendListingDataToKeen::class,
        ],
        \Kabooodle\Bus\Events\Listings\ListingsWereQueued::class => [
            \Kabooodle\Bus\Handlers\Events\Listings\NotifyListingsWereQueued::class,
        ],
        \Kabooodle\Bus\Events\Listings\ListingItemWasQueued::class => [
            \Kabooodle\Bus\Handlers\Events\Listings\NotifyListingItemWasQueued::class,
        ],
        // LISTING ITEMS
        \Kabooodle\Bus\Events\Listings\ListingItemWasDeleted::class => [
            \Kabooodle\Bus\Handlers\Events\Listings\HandleListingItemWasDeleted::class
        ],

        // PROFILE EVENTS
        \Kabooodle\Bus\Events\Profile\UserWasSubscribedToPlanEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Profile\UserWasSubscribedToPlanEventHandler::class,
            \Kabooodle\Bus\Handlers\Events\User\CreateKeenPolicyForSubscribedUser::class,
        ],

        // SHIPPING EVENTS
        \Kabooodle\Bus\Events\Shipping\ShippingTransactionWasCreatedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Shipping\MoveLabelToS3Handler::class,
            \Kabooodle\Bus\Handlers\Events\Shipping\DispatchShippingWebhookHandler::class,
            \Kabooodle\Bus\Handlers\Events\Shipping\SendShippingDataToKeen::class,
        ],
        \Kabooodle\Bus\Events\Shipping\ShippingLabelPrinted::class => [
            \Kabooodle\Bus\Handlers\Events\Shipping\ShippingLabelPrintedEventHandler::class
        ],
        // SHIPPING STATUS UPDATED
        \Kabooodle\Bus\Events\Shipping\ShippingTransactionStatusUpdatedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Shipping\NotifyUsersShippingStatusUpdatedHandler::class
        ],

        // SOCIAL EVENTS
        \Kabooodle\Bus\Events\Social\UserFacebookCredentialsRevokedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Social\UserFacebookCredentialsRevokedEventHandler::class
        ],

        // SUBSCRIPTION EVENTS
        \Kabooodle\Bus\Events\Subscriptions\InvoicePaymentFailed::class => [
            \Kabooodle\Bus\Handlers\Events\Subscription\NotifyUserInvoicePaymentFailed::class
        ],
        \Kabooodle\Bus\Events\Subscriptions\SubscriptionCancelled::class => [
            \Kabooodle\Bus\Handlers\Events\Subscription\NotifyUserSubscriptionCancelled::class
        ],

        // USER EVENTS
        \Kabooodle\Bus\Events\User\UserWasCreatedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\User\AddNewUserToGenericTrial::class,
            \Kabooodle\Bus\Handlers\Events\User\AddNewUserToAllNotificationTypes::class,
            \Kabooodle\Bus\Handlers\Events\User\SendNewUserWelcomeNotifications::class,
            \Kabooodle\Bus\Handlers\Events\User\AssignUserGenericAvatar::class,
        ],
        \Kabooodle\Bus\Events\User\UserUpgradedToGenericTrial::class => [
            \Kabooodle\Bus\Handlers\Events\User\SendUserOnNewGenericTrialNotifications::class
        ],
        \Kabooodle\Bus\Events\User\UserLoggedInEvent::class => [
            \Kabooodle\Bus\Handlers\Events\User\UserLoggedInListener::class
        ],
        \Kabooodle\Bus\Events\User\ReferralHasQualified::class => [
            \Kabooodle\Bus\Handlers\Events\User\StoreQualifiedReferral::class
//            \Kabooodle\Bus\Handlers\Events\User\NotifyReferredByOfQualifiedReferral::class
        ],

        // EMAIL EVENTS
        \Kabooodle\Bus\Events\Email\EmailWasCreatedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\Email\EmailWasCreatedListener::class,
        ],
        \Kabooodle\Bus\Events\Email\EmailWasVerifiedEvent::class => [
            \Kabooodle\Bus\Handlers\Events\User\CheckAndActivateUserAccount::class
        ],
    ];
    /**
     * @var array
     */
    protected $subscribe = [
        \Kabooodle\Bus\Handlers\Events\BroadcastEventListener::class
    ];
}
