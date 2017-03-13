<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use DB;
use Carbon\Carbon;
use Kabooodle\Models\User;
use Kabooodle\Models\Listings;
use Kabooodle\Models\ListingItems;
use Kabooodle\Services\Listings\ListingsService;
use Kabooodle\Models\Listing\FacebookListingOptions;
use Kabooodle\Bus\Events\Listings\ListingScheduledEvent;
use Kabooodle\Bus\Commands\Listings\ScheduleListingCommand;
use Kabooodle\Bus\Commands\Listings\ScheduleFacebookListingCommand;
use Kabooodle\Foundation\Exceptions\Listings\ListingExceedsHourlyLimitException;

/**
 * Class ScheduleFacebookListingCommandHandler
 */
class ScheduleFacebookListingCommandHandler extends AbstractScheduleListingsCommandHandler
{
    /**
     * @var bool
     */
    public $postingNow = false;

    /**
     * @var ListingsService
     */
    public $listingsService;

    /**
     * @param ListingsService $listingsService
     */
    public function __construct(ListingsService $listingsService)
    {
        $this->listingsService = $listingsService;
    }

    /**
     * @param ScheduleFacebookListingCommand $command
     *
     * @return mixed
     */
    public function handle(ScheduleFacebookListingCommand $command)
    {
        // Set a timestamp of now we can reuse for consistency.
        $this->now = Carbon::now();

        /** @var User $actor */
        $actor = $command->getActor();

        /** @var Carbon $scheduledFor */
        $scheduledFor = $this->normalizeScheduledDateTime($command->getFacebookListingOptions()->getStartsAt());

        $this->assertFacebookCredentialsAreValid($actor);

        $events = collect([]);

        DB::transaction(function () use ($actor, $scheduledFor, $command, $events) {

            foreach($command->getFacebookSales() as $facebookGroup) {
                /** @var Listings $listing */
                $listing = $this->buildListing($actor, $facebookGroup['id'], $scheduledFor, $command->getFacebookListingOptions());

                // Build an array of InventoryItems containing facebook listings associated
                // to the parent listing just created.
                $facebookInventoryItems = $this->buildListingItems($actor, $listing, $facebookGroup['sales'], $command->getFacebookListingOptions());

                // Because facebook throttles API requests to 200/calls an hour, we need to make sure
                // The requested time schedule doesn't already have "200 calls" scheduled.
                // If it does, an exception will be thrown.
                $this->assertListingDoesNotConflictWithExistingListing($scheduledFor, $actor, $facebookInventoryItems);

                $listing->listingItems()->saveMany($facebookInventoryItems);

                $events->push(new ListingScheduledEvent($actor->id, $listing->id));
            }
        });

        if ($events->count() > 0) {
            foreach($events as $event) {
                event($event);
            }
        }
    }

    /**
     * @param User        $user
     * @param int|null    $existingId
     * @param Carbon|null $scheduledFor
     * @param null        $options
     *
     * @return Listings
     */
    public function buildListing(User $user, int $existingId = null, Carbon $scheduledFor = null, $options = null)
    {
        $listing = parent::buildListing($user, $existingId, $scheduledFor);
        $listing->fb_group_node_id = $existingId;
        $listing->type = Listings::TYPE_FACEBOOK;

        if ($options->getEndsAt()) {
            $listing->scheduled_until = $options->getEndsAt();
        }

        if ($options->getClaimingStartsAt()) {
            $listing->claimable_at = $options->getClaimingStartsAt();
        }

        if ($options->getClaimingEndsAt()) {
            $listing->claimable_until = $options->getClaimingEndsAt();
        }

        $listing->save();

        return $listing;
    }

    /**
     * @param User                   $actor
     * @param Listings               $listing
     * @param array                  $sales
     * @param FacebookListingOptions $options
     *
     * @return array
     */
    public function buildListingItems(User $actor, Listings $listing, array $sales, FacebookListingOptions $options)
    {
        $listableItems = [];

        if (count($sales) > 0) {
            // Iterate over the facebook albums and figure out what items were assigned to each album
            foreach ($sales as $sale) {

                // If this album doesn't have an items, then ignore it.
                // This is a sanity check.
                if (!isset($sale['listables']) || count($sale['listables']) == 0) {
                    continue;
                }

                // Loop over each of the items
                foreach ($sale['listables'] as $listableItem) {

                    // Skip items that do not belong to the user.
                    if (! $this->listableItemBelongsToUser($listableItem['id'], $actor)) {
                        continue;
                    }

                    $listingItem = new ListingItems;
                    $listingItem->listing_id = $listing->id;
                    $listingItem->owner_id = $actor->id;
                    $listingItem->fb_group_node_id = $sale['sale']['id'];
                    $listingItem->fb_album_node_id = $sale['album']['id'];
                    $listingItem->listable_id = $listableItem['id'];
                    $listingItem->item_message = $options->getItemMessage();

                    // Copy the type and status from the parent listing.
                    // Status may actually change and be different, below otherwise they start the same.
                    $listingItem->type = $listing->type;
                    $listingItem->status = $listing->status;
                    $listingItem->status_updated_at = $this->now;

                    // Disabled for now -- JT January 9, 2017
                    // There really is no way to know if its a duplicate at this time.
                    // Flag duplicates as ignored listings.
                    // We do not actually "skip" them because we want to provide full transparency to the user.
//                    if ($this->itemAlreadyInFacebookAlbum($actor, $sale['album']['id'], $listableItem['id'])) {
//                        $listingItem->ignore = true;
//                        $listingItem->status = ListingItems::STATUS_IGNORED_DUPLICATE;
//                    }

                    $listableItems[] = $listingItem;
                }
            }
        }

        return $listableItems;
    }

    /**
     * @param User $user
     * @param int  $facebookAlbumId
     * @param int  $inventoryId
     *
     * @return
     */
    protected function itemAlreadyInFacebookAlbum(User $user, int $facebookAlbumId, int $inventoryId)
    {
        $user->load('listingItemsInFacebook');

        return $user->listingItemsInFacebook->filter(function ($item) use ($facebookAlbumId, $inventoryId) {
            return $item->fb_album_node_id == $facebookAlbumId && $item->inventory_id == $inventoryId;
        })->first();
    }

    /**
     * Check if the listing's datetime block does not already have a scheduled/queued listing for the user.
     * If it does, we will throw an exception and be done. This is to help simplify facebook's throttling nightmare.
     *
     * @param Carbon $dateTime
     * @param User   $actor
     * @param array $facebookListedItems
     *
     * @return bool
     *      * @throws ListingExceedsHourlyLimitException
     */
    public function assertListingDoesNotConflictWithExistingListing(Carbon $dateTime, User $actor, array $facebookListedItems)
    {
        // Get the date time, and find 60 minutes from this time as the max and the min is the scheduled time.
        $minDateTime = $dateTime->format('Y-m-d H:i:s.u');
        $maxDateTime = $dateTime->addMinutes(self::MAX_LOOKAHEAD_MINUTES)->format('Y-m-d H:i:s.u');

        $this->listingsService->assertNumberOfItemsDoesNotExceedFacebookHourlyQuota(
            $actor,
            $minDateTime,
            count($facebookListedItems)
        );

        return true;
    }

    /**
     * @param User $user
     */
    public function assertFacebookCredentialsAreValid(User $user)
    {
        $this->listingsService->assertFacebookAccessTokenIsValid($user);
    }
}
