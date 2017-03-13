<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Listings;

use Kabooodle\Models\User;
use Kabooodle\Models\Listings;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Class ListingScheduledEvent
 */
final class ListingScheduledEvent
{
    use InteractsWithQueue;

    /**
     * @var
     */
    public $actorId;

    /**
     * @var int
     */
    public $listingId;

    /**
     * @param int $actorId
     * @param int $listingId
     */
    public function __construct(int $actorId, int $listingId)
    {
        $this->actorId = $actorId;
        $this->listingId = $listingId;
    }

    /**
     * @return int
     */
    public function getActorId(): int
    {
        return $this->actorId;
    }

    /**
     * @return int
     */
    public function getListingId(): int
    {
        return $this->listingId;
    }
}
