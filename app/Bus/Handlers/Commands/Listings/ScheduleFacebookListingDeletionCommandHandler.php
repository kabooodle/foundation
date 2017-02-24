<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use Carbon\Carbon;
use Kabooodle\Models\Listings;
use Kabooodle\Services\Listings\ListingsService;
use Kabooodle\Bus\Commands\Listings\ScheduleFacebookListingDeletionCommand;

/**
 * Class ScheduleFacebookListingDeletionCommandHandler
 */
class ScheduleFacebookListingDeletionCommandHandler
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
     * @param ScheduleFacebookListingDeletionCommand $command
     */
    public function handle(ScheduleFacebookListingDeletionCommand $command)
    {
        $listing = Listings::where('owner_id', '=', $command->getOwner()->id)
            ->with('listingItems')
            ->findOrFail($command->getListingId());

        /** @var Carbon $startTime */
        $startTime = $this->listingService->findAvailableTimeToScheduleDeletion(
            $command->getOwner(),
            Carbon::now()->addMinutes(60),
            $listing->listingItems->count()
        );

        $listing->status = Listings::STATUS_SCHEDULED_DELETE;
        $listing->status_updated_at = Carbon::now();
        $listing->scheduled_for_deletion = $startTime->toDateTimeString();
        $listing->status_history = 'Scheduled for facebook deletion';
        $listing->save();
    }
}
