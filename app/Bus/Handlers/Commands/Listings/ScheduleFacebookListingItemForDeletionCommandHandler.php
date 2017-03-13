<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use Carbon\Carbon;
use Kabooodle\Models\ListingItems;
use Kabooodle\Services\Listings\ListingsService;
use Kabooodle\Bus\Commands\Listings\ScheduleFacebookListingItemForDeletionCommand;

/**
 * Class ScheduleFacebookListingItemForDeletionCommandHandler
 */
class ScheduleFacebookListingItemForDeletionCommandHandler
{
    /**
     * @var ListingsService
     */
    public $listingService;

    /**
     * @param ListingsService $listingsService
     */
    public function __construct(ListingsService $listingsService)
    {
        $this->listingService = $listingsService;
    }

    /**
     * @param ScheduleFacebookListingItemForDeletionCommand $command
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model
     */
    public function handle(ScheduleFacebookListingItemForDeletionCommand $command)
    {
        $listingItem = ListingItems::where('owner_id', '=', $command->getOwner()->id)
            ->findOrFail($command->getListingItemId());

        $lookAhead = Carbon::now()->addMinutes(4);

        /** @var Carbon $startTime */
        $startTime = $this->listingService->findAvailableTimeToScheduleDeletion(
            $command->getOwner(),
            $lookAhead,
            (int) 1
        );

        $listingItem->status = ListingItems::STATUS_SCHEDULED_DELETE;
        $listingItem->status_updated_at = Carbon::now();
        $listingItem->scheduled_for_deletion = $startTime->toDateTimeString();
        $listingItem->status_history = 'Scheduled for facebook deletion';
        $listingItem->save();

        return $listingItem;
    }
}