<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use DB;
use Carbon\Carbon;
use Kabooodle\Models\User;
use Kabooodle\Models\Listings;
use Kabooodle\Models\ListingItems;
use Kabooodle\Models\Listing\FacebookListingOptions;
use Facebook\Exceptions\FacebookAuthenticationException;
use Kabooodle\Bus\Events\Listings\ListingScheduledEvent;
use Kabooodle\Services\Social\Facebook\FacebookSdkService;
use Kabooodle\Bus\Commands\Listings\ScheduleListingCommand;
use Kabooodle\Foundation\Exceptions\Listings\ListingConflictsWithExistingListingException;

/**
 * Class ScheduleListingCommandHandler
 */
class ScheduleListingCommandHandler
{
    const EMPTY_DATE_LOOKAHEAD_MINUTES = 5;
    const MAX_LISTINGS_PER_HOUR = 200;
    const MAX_LOOKAHEAD_MINUTES = 59;

    /**
     * @var bool
     */
    public $postingNow = false;

    /**
     * @var bool
     */
    public $isFacebookListing = false;

    /**
     * @var Carbon
     */
    public $now;

    /**
     * @param ScheduleListingCommand $command
     * @throws FacebookAuthenticationException
     * @return mixed
     */
    public function handle(ScheduleListingCommand $command)
    {
        // Set a timestamp of now we can reuse for consistency.
        $this->now = Carbon::now();

        /** @var User $actor */
        $actor = $command->getActor();

        /** @var Carbon $scheduledFor */
        $scheduledFor = $this->normalizeScheduledDateTime($command->getFacebookListingOptions()->getStartsAt());

        if ($command->getFacebookGroupId()) {
            $this->assertFacebookCredentialsAreValid();

            $this->isFacebookListing = true;
        }

        // Reference holding all listings saved
        $totalSavedListings = [];

        return DB::transaction(function () use ($actor, $scheduledFor, $command, $totalSavedListings) {
            /** @var Listings $listing */
            $listing = $this->buildListing($command, $scheduledFor);

            // We have some special logic for facebook listings
            if ($this->isFacebookListing) {
                // Build an array of ListedItems containing facebook listings associated
                // to the parent listing just created.
                $facebookListedItems = $this->buildFacebookListingItems($listing, $command);

                // Because facebook throttles API requests to 200/calls an hour, we need to make sure
                // The requested time schedule doesn't already have "200 calls" scheduled.
                // If it does, an exception will be thrown.
                $this->assertListingDoesNotConflictWithExistingListing($scheduledFor, $actor, $facebookListedItems);

                $listing->listingItems()->saveMany($facebookListedItems);

                $totalSavedListings[] = $facebookListedItems;
            } else {
                $flashsaleListedItems = $this->buildFlashsaleListingItems($listing, $command);

                $listing->listingItems()->saveMany($flashsaleListedItems);

                $totalSavedListings[] = $flashsaleListedItems;
            }

            event(new ListingScheduledEvent($actor->id, $listing->id));

            return $listing;
        });
    }

    /**
     * @param ScheduleListingCommand $command
     * @param Carbon                 $scheduledFor
     *
     * @return Listings
     */
    public function buildListing(ScheduleListingCommand $command, Carbon $scheduledFor)
    {
        /** @var FacebookListingOptions $options */
        $options = $command->getFacebookListingOptions();

        $listing = new Listings;
        $listing->owner_id = $command->getActor()->id;
        $listing->scheduled_for = $scheduledFor;

        if ($options->getEndsAt()) {
            $listing->scheduled_until = $options->getEndsAt();
        }

        if ($options->getClaimingStartsAt()) {
            $listing->claimable_at = $options->getClaimingStartsAt();
        }

        if ($options->getClaimingEndsAt()) {
            $listing->claimable_until = $options->getClaimingEndsAt();
        }

        $listing->status = Listings::STATUS_SCHEDULED;
        $listing->status_updated_at = $this->now;

        // Are we making a facebook post or flashsale?
        if ($this->isFacebookListing) {
            $listing->fb_group_node_id = $command->getFacebookGroupId();
            $listing->type = Listings::TYPE_FACEBOOK;
        } else {
            // handle flash sale.
            $listing->flashsale_id = $command->getFlashSaleId();
            $listing->type = Listings::TYPE_FLASHSALE;
        }
        $listing->save();

        return $listing;
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
     * @param User $user
     * @param int  $flashSaleId
     * @param int  $inventoryId
     *
     * @return mixed
     */
    protected function itemAlreadyInFlashsale(User $user, int $flashSaleId, int $inventoryId)
    {
        $user->load('listingItemsInFlashsales');

        return $user->listingItemsInFlashsales->filter(function ($item) use ($flashSaleId, $inventoryId) {
            return $item->flashsale_id == $flashSaleId && $item->inventory_id == $inventoryId;
        })->first();
    }

    /**
     * @param string|null $dateTime
     *
     * @return Carbon
     */
    public function normalizeScheduledDateTime($dateTime = null)
    {
        // If the dateTime is null, then we will schedule this posting for
        // 5 minutes from now.
        if (!$dateTime || is_null($dateTime)) {
            $this->postingNow = true;

            return Carbon::now()->addMinutes(self::EMPTY_DATE_LOOKAHEAD_MINUTES);
        }

        return $dateTime;
    }

    /**
     * @param Listings               $listing
     * @param ScheduleListingCommand $command
     *
     * @return array
     */
    public function buildFacebookListingItems(Listings $listing, ScheduleListingCommand $command)
    {
        $facebookAlbums = $command->getFacebookAlbums();
        $actor = $command->getActor();
        $listedItems = [];

        if (count($facebookAlbums) > 0) {
            // Iterate over the facebook albums and figure out what items were assigned to each album
            foreach ($facebookAlbums as $facebookAlbum) {

                // If this album doesn't have an items, then ignore it.
                // This is a sanity check.
                if (!isset($facebookAlbum['items']) || count($facebookAlbum['items']) == 0) {
                    continue;
                }

                // Loop over each of the items
                foreach ($facebookAlbum['items'] as $listedItem) {

                    // Skip inventory items that do not belong to the user.
                    if (! $this->listedItemBelongsToUser($listedItem['id'], $actor)) {
                        continue;
                    }

                    $listingItem = new ListingItems;
                    $listingItem->listing_id = $listing->id;
                    $listingItem->owner_id = $actor->id;
                    $listingItem->fb_group_node_id = $command->getFacebookGroupId();
                    $listingItem->fb_album_node_id = $facebookAlbum['id'];
                    $listingItem->inventory_id = $listedItem['id'];
                    $listingItem->item_message = $command->getFacebookListingOptions()->getItemMessage();

                    // Copy the type and status from the parent listing.
                    // Status may actually change and be different, below otherwise they start the same.
                    $listingItem->type = $listing->type;
                    $listingItem->status = $listing->status;
                    $listingItem->status_updated_at = $this->now;

                    // Disabled for now -- JT January 9, 2017
                    // There really is no way to know if its a duplicate at this time.
                    // Flag duplicates as ignored listings.
                    // We do not actually "skip" them because we want to provide full transparency to the user.
                    if ($this->itemAlreadyInFacebookAlbum($actor, $facebookAlbum['id'], $listedItem['id'])) {
                        $listingItem->ignore = true;
                        $listingItem->status = ListingItems::STATUS_IGNORED_DUPLICATE;
                    }

                    $listedItems[] = $listingItem;
                }
            }
        }

        return $listedItems;
    }

    /**
     * @param Listings               $listing
     * @param ScheduleListingCommand $command
     *
     * @return array
     */
    public function buildFlashsaleListingItems(Listings $listing, ScheduleListingCommand $command)
    {
        $selectedItems = $command->getSelectedItems();
        $actor = $command->getActor();
        $listedItems = [];

        if (count($selectedItems) > 0) {
            // Iterate over the facebook albums and figure out what items were assigned to each album
            foreach ($selectedItems as $selectedItem) {

                // Skip inventory items that do not belong to the user.
                if (! $this->listedItemBelongsToUser($selectedItem['id'], $actor)) {
                    continue;
                }

                $listingItem = new ListingItems;
                $listingItem->listing_id = $listing->id;
                $listingItem->owner_id = $actor->id;
                $listingItem->inventory_id = $selectedItem['id'];
                $listingItem->flashsale_id = $command->getFlashSaleId();
                $listingItem->item_message = $command->getFacebookListingOptions()->getItemMessage();

                // Copy the type and status from the parent listing.
                // Status may actually change and be different, below otherwise they start the same.
                $listingItem->type = $listing->type;
                $listingItem->status = $listing->status;
                $listingItem->status_updated_at = $this->now;

                // Flag duplicates as ignored listings.
                // We do not actually "skip" them because we want to provide full transparency to the user.
                if ($this->itemAlreadyInFlashsale($actor, $command->getFlashSaleId(), $selectedItem['id'])) {
                    $listingItem->ignore = true;
                    $listingItem->status = ListingItems::STATUS_IGNORED_DUPLICATE;
                }

                $listedItems[] = $listingItem;
            }
        }

        return $listedItems;
    }

    /**
     * TODO: is it faster to run a normal sql query vs filtering through the eager loaded collection?
     *
     * @param int  $inventoryId
     * @param User $actor
     *
     * @return mixed
     */
    public function listedItemBelongsToUser(int $inventoryId, User $actor)
    {
        return $actor->inventory->find($inventoryId);
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
     * @throws ListingConflictsWithExistingListingException
     */
    public function assertListingDoesNotConflictWithExistingListing(Carbon $dateTime, User $actor, array $facebookListedItems)
    {
        // Get the date time, and find 60 minutes from this time as the max and the min is the scheduled time.
        $minDateTime = $dateTime->format('Y-m-d H:i:s.u');
        $maxDateTime = $dateTime->addMinutes(self::MAX_LOOKAHEAD_MINUTES)->format('Y-m-d H:i:s.u');

        $hourlyQuoteExceeded = ListingItems::checkIfAttemptedListingExceedsHourlyQuota(
            $actor->id,
            $minDateTime,
            $maxDateTime,
            count($facebookListedItems)
        );

        if ($hourlyQuoteExceeded) {
            throw new ListingConflictsWithExistingListingException;
        }

        return true;
    }

    /**
     * @return bool
     * @throws FacebookAuthenticationException
     */
    public function assertFacebookCredentialsAreValid()
    {
        $fb = app(FacebookSdkService::class);
        if (! $fb->testAccessToken()){
            throw new FacebookAuthenticationException;
        }

        return true;
    }
}
