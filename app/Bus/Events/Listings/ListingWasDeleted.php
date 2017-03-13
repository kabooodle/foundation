<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Listings;

/**
 * Class ListingWasDeleted
 */
final class ListingWasDeleted
{
    /**
     * @var int
     */
    public $listingId;

    /**
     * @param int $listingId
     */
    public function __construct(int $listingId)
    {
        $this->listingId = $listingId;
    }

    /**
     * @return int
     */
    public function getListingId(): int
    {
        return $this->listingId;
    }
}
