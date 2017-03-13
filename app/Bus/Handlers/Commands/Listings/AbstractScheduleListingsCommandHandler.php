<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use Carbon\Carbon;
use Kabooodle\Models\User;
use Kabooodle\Models\Listings;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\InventoryGrouping;

/**
 * Class AbstractScheduleListingsCommandHandler
 */
abstract class AbstractScheduleListingsCommandHandler
{
    const EMPTY_DATE_LOOKAHEAD_MINUTES = 5;
    const MAX_LISTINGS_PER_HOUR = 200;
    const MAX_LOOKAHEAD_MINUTES = 59;

    /**
     * @var Carbon
     */
    public $now;


    public function __construct()
    {
        // Set a timestamp of now we can reuse for consistency.
        $this->now = Carbon::now();
    }

    /**
     * @param null $dateTime
     * @param int  $lookahead
     *
     * @return null|static
     */
    public function normalizeScheduledDateTime($dateTime = null, $lookahead = self::EMPTY_DATE_LOOKAHEAD_MINUTES)
    {
        // If the dateTime is null, then we will schedule this posting for
        // 5 minutes from now.
        if (!$dateTime || is_null($dateTime)) {
            $this->postingNow = true;

            return Carbon::now()->addMinutes($lookahead);
        }

        return $dateTime;
    }

    /**
     * TODO: is it faster to run a normal sql query vs filtering through the eager loaded collection?
     *
     * @param int $listableId
     * @param User $actor
     *
     * @return bool
     */
    public function listableItemBelongsToUser(int $listableId, User $actor)
    {
        return $actor->listables->where('id', $listableId)->first();
    }

    /**
     * @param User        $user
     * @param int|null    $existingId
     * @param Carbon|null $scheduledFor
     * @param null        $options
     *
     * @return Listings
     */
    public function buildListing(User $user, int $existingId = null,  Carbon $scheduledFor = null, $options = null)
    {
        $listing = new Listings;
        $listing->owner_id = $user->id;
        $listing->scheduled_for = $scheduledFor;
        $listing->status = Listings::STATUS_SCHEDULED;
        $listing->status_updated_at = $this->now;

        return $listing;
    }


    /**
     * @param Listings $listing
     * @param int      $inventoryId
     *
     * @return mixed
     */
    protected function itemAlreadyInSale(Listings $listing, int $inventoryId)
    {
        $listingItems = $listing->listingItems;

        return $listingItems->filter(function ($listingItem) use ($inventoryId) {
            return $listingItem->inventory_id == $inventoryId;
        })->first();
    }
}
