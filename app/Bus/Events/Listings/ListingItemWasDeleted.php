<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Listings;

use Kabooodle\Models\ListingItems;

/**
 * Class ListingItemWasDeleted
 */
final class ListingItemWasDeleted
{
    /**
     * @var ListingItems
     */
    public $listingItem;

    /**
     * @param ListingItems $listingItem
     */
    public function __construct(ListingItems $listingItem)
    {
        $this->listingItem = $listingItem;
    }

    /**
     * @return ListingItems
     */
    public function getListingItem(): ListingItems
    {
        return $this->listingItem;
    }
}
