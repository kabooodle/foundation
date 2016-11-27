<?php

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use DB;
use Carbon\Carbon;
use Kabooodle\Models\User;
use Kabooodle\Models\Listings;
use Kabooodle\Models\ListingItems;
use Illuminate\Foundation\Bus\DispatchesJobs;
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

    use DispatchesJobs;

    /**
     * @var bool
     */
    public $postingNow = false;

    /**
     * @var Carbon
     */
    public $now;

    /**
     * @param ScheduleListingCommand $command
     * @return Listings
     */
    public function handle(ScheduleListingCommand $command)
    {
        // Set a single timestamp;
        $this->now = Carbon::now();

        /** @var User $actor */
        $actor = $command->getActor();

        /** @var Carbon $scheduledFor */
        $scheduledFor = $this->normalizeScheduledDateTime($command->getScheduledFor());

        // If we are dealing with a facebook listing, then we need to make a few assertions.
        if($command->getFacebookGroupId()) {
            // Because facebook throttles API requests to 200/calls an hour, we need to make sure
            // The requested time schedule doesn't already have "200 calls" scheduled.
            // If it does, an exception will be thrown.
            $this->assertListingDoesNotConflictWithExistingListing($scheduledFor, $actor);
        }

        // We should be good to proceed with saving the listings.
        return DB::transaction(function() use ($actor, $scheduledFor, $command) {
            $listing = new Listings;
            $listing->name = $command->getName();
            $listing->owner_id = $actor->id;
            $listing->scheduled_for = $scheduledFor;
            $listing->status = Listings::STATUS_SCHEDULED;
            $listing->status_updated = $this->now;

            if($facebookId = $command->getFacebookGroupId()) {
                $listing->fb_group_node_id = $facebookId;
                $listing->type = Listings::TYPE_FACEBOOK;
            } else {
                $listing->flashsale_id = $command->getFlashSales();
                $listing->type = Listings::TYPE_FLASHSALE;
            }
            $listing->save();

            /** @var array $inventoryItems */
            $inventoryItems = $this->buildListingItemsArray($command->getInventoryItemIds(), $listing->id);

            $listing->items()->saveMany($inventoryItems);

            return $listing;
        });
    }

    /**
     * @param ScheduleListingCommand $command
     * @param int $listingId
     * @return array
     */
    public function buildListingItemsArray(ScheduleListingCommand $command, int $listingId)
    {
        $listingItems = [];
        foreach($inventoryItemIds as $inventoryItemId) {
            $listingItem = new ListingItems;
            $listingItem->listing_id = $listingId;
            $listingItem->owner_id = $command->getActor()->id;
            $listingItem->fb_group_node_id = '';
            $listingItem->fb_album_node_id = '';
            $listingItem->flashsale_id = '';
            $listingItem->inventory_id = $inventoryItemId;
            $listingItem->status_updated_at = $this->now;
            $listingItem->status = ListingItems::STATUS_SCHEDULED;
            if($facebookId = $command->getFacebookGroupId()) {
                $listingItem->fb_group_node_id = $facebookId;
                $listingItem->fb_album_node_id = $command->getFacebookAlbumIds(); // TODO: Fix
                $listingItem->type = Listings::TYPE_FACEBOOK;
            } else {
                $listingItem->flashsale_id = $command->getFlashsaleId();
                $listingItem->type = Listings::TYPE_FLASHSALE;
            }


            $listingItems[] = $listingItem;
        }

        return $listingItems;
    }

    /**
     * @param string|null $dateTime
     * @return Carbon
     */
    public function normalizeScheduledDateTime(string $dateTime = null)
    {
        // If the dateTime is null, then we will schedule this posting for
        // 5 minutes from now.
        if (!$this->hasScheduledDate($dateTime)) {
            $this->postingNow = true;
            return Carbon::now()->addMinutes(EMPTY_DATE_LOOKAHEAD_MINUTES);
        }

        return Carbon::createFromTimestamp(strtotime($dateTime));
    }

    /**
     * @param null $scheduledDate
     * @return bool
     */
    public function hasScheduledDate($scheduledDate = null)
    {
        return $scheduledDate ? true : false;
    }

    /**
     * @param Carbon $dateTime
     * @param User $actor
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
        if(count($results) > 0) {
            throw new ListingConflictsWithExistingListingException($results);
        }

        return true;
    }
}
