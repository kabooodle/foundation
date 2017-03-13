<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use DB;
use Kabooodle\Models\Listings;
use Kabooodle\Models\ListingItems;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Events\Listings\ListingWasDeleted;
use Kabooodle\Bus\Commands\Listings\DeleteListingCommand;
use Kabooodle\Repositories\Listings\ListingsRepositoryInterface;
use Kabooodle\Bus\Commands\Claim\RejectClaimForInventoryItemCommand;
use Kabooodle\Bus\Commands\Listings\ScheduleFacebookListingItemForDeletionCommand;

/**
 * Class DeleteListingCommandHandler
 */
class DeleteListingCommandHandler
{
    use DispatchesJobs;

    /**
     * @var ListingsRepositoryInterface
     */
    protected $listingRepository;

    /**
     * @param ListingsRepositoryInterface $listingsRepository
     */
    public function __construct(ListingsRepositoryInterface $listingsRepository)
    {
        $this->listingRepository = $listingsRepository;
    }

    /**
     * @param DeleteListingCommand $command
     *
     * @return void
     */
    public function handle(DeleteListingCommand $command)
    {
        DB::transaction(function() use ($command) {
            $actor = $command->getActor();
            $listing = Listings::where('owner_id', '=', $actor->id)
                ->with('listingItems', 'pendingClaims')
                ->findOrFail($command->getListingId());

            // We delete all children and ancestors of the listing NOW instead of in an event listener.

            // Delete all listing items
            if ($listing->listingItems->count() > 0 ) {
                foreach($listing->listingItems as $item) {

                    // Delete item watchers
                    if ($item->watchers->count() > 0 ){
                        foreach ($item->watchers as $watcher) {
                            $watcher->delete();
                        }
                    }
// Removed because its weird to delete but not really delete...
//                    if ($item->status <> ListingItems::STATUS_QUEUED_DELETE && $item->status <> ListingItems::STATUS_DELETED) {
//                        $job = new ScheduleFacebookListingItemForDeletionCommand($actor, $item->id);
//                        $this->dispatch($job);
//                    } else {@jordan
                        $item->delete();
//                    }
                }
            }

            // Reject all pending claims.
            // Claims are hasManyThrough, and are really pending claims on listing items :)
            if ($listing->pendingClaims->count() > 0) {
                foreach($listing->pendingClaims as $claim){
                    $job = new RejectClaimForInventoryItemCommand($command->getActor(), $claim->uuid, 'Listing deleted by owner');
                    $this->dispatchNow($job);
                }
            }

            $listing->delete();

           event(new ListingWasDeleted($command->getListingId()));
       });
    }
}
