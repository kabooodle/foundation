<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Listings;

use Kabooodle\Models\User;

/**
 * Class ScheduleFacebookListingItemForDeletionCommand
 */
final class ScheduleFacebookListingItemForDeletionCommand
{
    /**
     * @var User
     */
    public $owner;

    /**
     * @var int
     */
    public $listingItemId;

    /**
     * @param User $owner
     * @param int  $listingItemId
     */
    public function __construct(User $owner, int $listingItemId)
    {
        $this->owner = $owner;
        $this->listingItemId = $listingItemId;
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
    public function getListingItemId(): int
    {
        return $this->listingItemId;
    }
}
