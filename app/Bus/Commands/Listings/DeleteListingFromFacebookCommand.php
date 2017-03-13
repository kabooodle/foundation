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
 * Class DeleteListingFromFacebookCommand
 */
final class DeleteListingFromFacebookCommand implements ShouldQueue
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
     * @param int $ownerId
     * @param int $listingId
     */
    public function __construct(int $ownerId, int $listingId)
    {
        $this->ownerId = $ownerId;
        $this->listingId = $listingId;
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
}
