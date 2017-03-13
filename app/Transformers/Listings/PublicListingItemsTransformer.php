<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Transformers\Listings;

use Kabooodle\Models\ListingItems;
use League\Fractal\TransformerAbstract;

/**
 * Class PublicListingItemsTransformer
 */
class PublicListingItemsTransformer extends TransformerAbstract
{
    /**
     * @param ListingItems $listingItem
     *
     * @return array
     */
    public function transform(ListingItems $listingItem)
    {
        return [
            'uuid' => $listingItem->uuid,
            'name' => $listingItem->name,
            'owner' => $listingItem->owner,
            'type' => $listingItem->type,
            'status' => $listingItem->status,
            'status_updated_at' => $listingItem->status_updated_at,
            'parent_sale_id' => $listingItem->typeIs(ListingItems::TYPE_FLASHSALE) ? $listingItem->flashsale_id : $listingItem->fb_album_node_id,
            'parent_sale' => $listingItem->typeIs(ListingItems::TYPE_FLASHSALE) ? $listingItem->flashsale : $listingItem->facebookAlbum,
            'created_at' => $listingItem->created_at,
            'updated_at' => $listingItem->updated_at
        ];
    }
}
