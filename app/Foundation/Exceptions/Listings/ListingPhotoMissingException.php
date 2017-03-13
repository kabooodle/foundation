<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Foundation\Exceptions\Listings;

use Exception;

/**
 * Class ListingPhotoMissingException
 */
class ListingPhotoMissingException extends Exception
{
    /**
     * @param string $listingItemId
     */
    public function __construct($listingItemId)
    {
        parent::__construct("Listing item [{$listingItemId}] is missing a photo.");
    }
}