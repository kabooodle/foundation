<?php

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use DB;
use Carbon\Carbon;
use Kabooodle\Models\User;
use Kabooodle\Models\Listings;
use Kabooodle\Models\ListingItems;
use Kabooodle\Bus\Commands\Listings\ScheduleListingCommand;
use Kabooodle\Foundation\Exceptions\Listings\ListingConflictsWithExistingListingException;

/**
 * Class ScheduleListingCommandHandler
 */
class ScheduleListingCommandHandler
{
    const EMPTY_DATE_LOOKAHEAD_MINUTES = 6;
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
     *
     * @return Listings
     */
    public function handle(ScheduleListingCommand $command)
    {
        // Set a timestamp we can reuse for consistency.
        $this->now = Carbon::now();

        /** @var User $actor */
        $actor = $command->getActor();

        /** @var Carbon $scheduledFor */
        $scheduledFor = $this->normalizeScheduledDateTime($command->getScheduledFor());

        // If we are dealing with a facebook listing, then we need to make 1 critical assertion.
        // May need to make mure in the future.
        if ($command->getFacebookGroupId()) {
            $this->isFacebookListing = true;
            // Because facebook throttles API requests to 200/calls an hour, we need to make sure
            // The requested time schedule doesn't already have "200 calls" scheduled.
            // If it does, an exception will be thrown.
            $this->assertListingDoesNotConflictWithExistingListing($scheduledFor, $actor);
        }

        // We should be good to proceed with saving the listings.
        return DB::transaction(function () use ($actor, $scheduledFor, $command) {
            $listing = new Listings;
            $listing->name = $command->getName();
            $listing->owner_id = $actor->id;
            $listing->scheduled_for = $scheduledFor;
            $listing->status = Listings::STATUS_SCHEDULED;
            $listing->status_updated = $this->now;

            // Are we making a facebook post or flashsale?
            if ($this->isFacebookListing) {
                $listing->fb_group_node_id = $command->getFacebookGroupId();
                $listing->type = Listings::TYPE_FACEBOOK;
            } else {
                // handle flash sale.
                $listing->flashsale_id = $command->getFlashSales();
                $listing->type = Listings::TYPE_FLASHSALE;
            }
            $listing->save();

            if ($this->isFacebookListing) {
                // Build an array of InventoryItems containing facebook listings.
                $facebookInventoryItems = $this->buildFacebookListings($listing, $command->getInventoryItemIds());
                if ($facebookInventoryItems) {
                    $listing->listingItems()->saveMany($facebookInventoryItems);
                }
            }

            return $listing;
        });
    }

    /**
     * @param Listings               $listing
     * @param ScheduleListingCommand $command
     *
     * @return array
     */
    public function buildFacebookListings(Listings $listing, ScheduleListingCommand $command)
    {
        $facebookAlbums = $command->getFacebookAlbums();
        $actor = $command->getActor();
        $listingItems = [];

        if (count($facebookAlbums) > 0) {
            // Iterate over the facebook albums and figure out what items were assigned to each album
            foreach ($facebookAlbums as $facebookAlbum) {

                // If this album doesn't have an items, then ignore it.
                // This is a sanity check.
                if (!isset($facebookAlbum['items']) || count($facebookAlbum['items']) == 0) {
                    continue;
                }

                // Loop over each of the items
                foreach ($facebookAlbum['items'] as $inventoryItem) {
                    $listingItem = new ListingItems;
                    $listingItem->listing_id = $listing->id;
                    $listingItem->owner_id = $actor->id;
                    $listingItem->fb_group_node_id = $command->getFacebookGroupId();
                    $listingItem->fb_album_node_id = $facebookAlbum['id'];
                    $listingItem->inventory_id = $inventoryItem['id'];

                    // Copy the type and status from the parent listing.
                    // Status may actually change and be different, below otherwise they start the same.
                    $listingItem->type = $listing->type;
                    $listingItem->status = $listing->status;
                    $listingItem->status_updated_at = $this->now;

                    // Ignore duplicate inventory items already in the facebook album
                    if (!$this->itemAlreadyInFacebookAlbum($actor, $facebookAlbum['id'], $inventoryItem['id'])) {
                        $listingItem->ignore = true;
                        $listingItem->status = ListingItems::STATUS_IGNORED_DUPLICATE;
                    }

                    $listingItems[] = $listingItem;
                }
            }
        }

        return $listingItems;
    }

    /**
     * @param User $user
     * @param int  $facebookAlbumId
     * @param int  $inventoryId
     *
     * @return mixed
     */
    protected function itemAlreadyInFacebookAlbum(User $user, int $facebookAlbumId, int $inventoryId)
    {
        $user->load('listingsOnFacebook');

        return $user->listingsOnFacebook->filter(function ($item) use ($facebookAlbumId, $inventoryId) {
            return $item->fb_album_node_id == $facebookAlbumId && $item->inventory_id == $inventoryId;
        })->first();
    }

    /**
     * @param string|null $dateTime
     *
     * @return Carbon
     */
    public function normalizeScheduledDateTime(string $dateTime = null)
    {
        // If the dateTime is null, then we will schedule this posting for
        // 5 minutes from now.
        if (!$dateTime || !is_null($dateTime)) {
            $this->postingNow = true;

            return Carbon::now()->addMinutes(EMPTY_DATE_LOOKAHEAD_MINUTES);
        }

        return Carbon::createFromTimestamp(strtotime($dateTime));
    }

    /**
     * Check if the listing's datetime block does not already have a scheduled/queued listing for the user.
     * If it does, we will throw an exception and be done. This is to help simplify facebook's throttling nightmare.
     *
     * @param Carbon $dateTime
     * @param User   $actor
     *
     * @return bool
     * @throws ListingConflictsWithExistingListingException
     */
    public function assertListingDoesNotConflictWithExistingListing(Carbon $dateTime, User $actor)
    {
        // Get the date time, and find 60 minutes from this time as the max and the min is the scheduled time.
        $minDateTime = $dateTime->format('Y-m-d H:i:s.u');
        $maxDateTime = $dateTime->addMinutes(self::MAX_LOOKAHEAD_MINUTES)->format('Y-m-d H:i:s.u');

        // Get all listing items where listing date is between min, max for userid;
        $results = ListingItems::queryGetItemsDuringDateTimeBlockForUser($actor->id, $minDateTime, $maxDateTime);

        // If ANY results are returned, we're just going to throw an exception and bail.
        if (count($results) > 0) {
            throw new ListingConflictsWithExistingListingException($results);
        }

        return true;
    }
}
