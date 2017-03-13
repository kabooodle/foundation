<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use DB;
use Kabooodle\Models\ListingItems;
use Kabooodle\Bus\Events\Listings\ListingItemWasDeleted;
use Kabooodle\Bus\Commands\Listings\DeleteListingItemCommand;

/**
 * Class DeleteListingItemCommandHandler
 */
class DeleteListingItemCommandHandler
{
    /**
     * @param DeleteListingItemCommand $command
     */
    public function handle(DeleteListingItemCommand $command)
    {
        DB::transaction(function() use ($command) {
            $listingItem = ListingItems::where('id', '=', $command->getListingItemId())
                ->where('listing_id', '=', $command->getListingId())
                ->where('owner_id', '=', $command->getActor()->id)
                ->firstOrFail();

            $listingItem->delete();

            event(new ListingItemWasDeleted($listingItem));
        });
    }
}
