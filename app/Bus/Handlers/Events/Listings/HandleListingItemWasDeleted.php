<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Listings;

use Kabooodle\Models\ListingItems;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Events\Listings\ListingItemWasDeleted;
use Kabooodle\Bus\Commands\Listings\DeleteListingItemFromFacebookCommand;

/**
 * Class HandleListingItemWasDeleted
 */
class HandleListingItemWasDeleted
{
    use DispatchesJobs;

    /**
     * @param ListingItemWasDeleted $event
     */
    public function handle(ListingItemWasDeleted $event)
    {
        $listingItem = $event->getListingItem();

        if ($listingItem->type == ListingItems::TYPE_FACEBOOK) {
            // Queue item for deletion from facebook.
            $this->dispatch(new DeleteListingItemFromFacebookCommand($listingItem->owner_id, $listingItem->listing_id, $listingItem->id));
        }
    }
}
