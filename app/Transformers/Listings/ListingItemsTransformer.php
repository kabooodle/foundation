<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Transformers\Listings;

use Kabooodle\Models\ListingItems;
use League\Fractal\TransformerAbstract;

/**
 * Class ListingItemsTransformer
 */
class ListingItemsTransformer extends TransformerAbstract
{
    /**
     * @param ListingItems $listingItem
     *
     * @return array
     */
    public function transform(ListingItems $listingItem)
    {
        return [
            'flashsale_id' => $listingItem->flashsale_id,
            'fb_album_node_id' => $listingItem->fb_album_node_id,
            'fb_group_node_id' => $listingItem->fb_group_node_id,
            'id' => $listingItem->id,
            'id_to_string' => $listingItem->id_to_string,
            'is_watched' => $listingItem->is_watched,
            'listed_item' => $listingItem->listedItem,
            'listable_id' =>$listingItem->listable_id,
            'listing_id' => $listingItem->listing_id,
            'make_available_at' => $listingItem->make_available_at? $listingItem->make_available_at->toDateTimeString() : null,
            'name' => $listingItem->name,
            'owner_id' => $listingItem->owner_id,
            'status' => $listingItem->scheduled,
            'type' => $listingItem->type,
            'uuid' => $listingItem->uuid,
            'watchers' => $listingItem->watchers->count(),
        ];
    }
}
