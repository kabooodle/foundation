<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Listings;

use Kabooodle\Models\User;

/**
 * Class ScheduleFacebookListingDeletionCommand
 */
final class ScheduleFacebookListingDeletionCommand
{
    /**
     * @var User
     */
    public $owner;

    /**
     * @var int
     */
    public $listingId;

    /**
     * @param User $owner
     * @param int  $listingId
     */
    public function __construct(User $owner, int $listingId)
    {
        $this->owner = $owner;
        $this->listingId = $listingId;
    }

    /**
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @return int
     */
    public function getListingId(): int
    {
        return $this->listingId;
    }
}
