<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Listings;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class DeleteListingItemFromFacebookCommand
 */
final class DeleteListingItemFromFacebookCommand implements ShouldQueue
{
    use InteractsWithQueue, Queueable;

    /**
     * @var int
     */
    public $ownerId;

    /**
     * @var int
     */
    public $listingId;

    /**
     * @var int
     */
    public $listingItemId;

    /**
     * @param int $ownerId
     * @param int $listingId
     * @param int $listingItemId
     */
    public function __construct(int $ownerId, int $listingId, int $listingItemId)
    {
        $this->ownerId = $ownerId;
        $this->listingId = $listingId;
        $this->listingItemId = $listingItemId;
    }

    /**
     * @return int
     */
    public function getOwnerId(): int
    {
        return $this->ownerId;
    }

    /**
     * @return int
     */
    public function getListingId(): int
    {
        return $this->listingId;
    }

    /**
     * @return int
     */
    public function getListingItemId(): int
    {
        return $this->listingItemId;
    }
}
