<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use DB;
use Carbon\Carbon;
use Kabooodle\Models\Listings;
use Kabooodle\Models\ListingItems;
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
     *
     * @return $this|null
     */
    public function handle(ScheduleFacebookListingDeletionCommand $command)
    {
        $listing = Listings::where('owner_id', '=', $command->getOwner()->id)
            ->with('listingItems')
            ->findOrFail($command->getListingId());

        if ($listing->listingItems->count() > 0 ) {
            DB::transaction(function() use ($listing, $command){
                $lookAhead = Carbon::now()->addMinutes(4);

                /** @var Carbon $startTime */
                $startTime = $this->listingService->findAvailableTimeToScheduleDeletion(
                    $command->getOwner(),
                    $lookAhead,
                    $listing->listingItems->count()
                );

                // It might be that all items for this listing have already been deleted manually.
                // Filter through the items and return all the items listed to FB so we can see.
                $listedToFb = $listing->listingItems->filter(function($listingItem){
                    return ($listingItem->fb_response_object_id <> '' || $listingItem->fb_response_object_id <> null);
                });

                // There are no items listed to facebook, so update the listing to deleted;
                if ($listedToFb->count() == 0) {
                    $listing->status = Listings::STATUS_DELETED;
                    $listing->status_updated_at = Carbon::now();
                    $listing->scheduled_for_deletion = null;
                } else {

                    $listing->status = Listings::STATUS_SCHEDULED_DELETE;
                    $listing->status_updated_at = Carbon::now();
                    $listing->scheduled_for_deletion = $startTime->toDateTimeString();
                    $listing->status_history = 'Scheduled for facebook deletion';

                    // Only queue the listing items that haven't been deleted and have a facebook object associated.
                    ListingItems::whereIn('id', $listing->listingItems->pluck('id')->toArray())
                        ->whereNotNull('fb_response_object_id')
                        ->whereNotIn('status', [ListingItems::STATUS_QUEUED_DELETE, ListingItems::STATUS_DELETED, ListingItems::STATUS_QUEUED_DELETE])
                        ->update([
                            'status' => ListingItems::STATUS_SCHEDULED_DELETE,
                            'status_updated_at' => Carbon::now(),
                            'scheduled_for_deletion' => $startTime->toDateTimeString(),
                            'status_history' => 'Scheduled for facebook deletion'
                        ]);
                }

                $listing->save();

                return $listing;
            });
        }

        return $listing->fresh();
    }
}
