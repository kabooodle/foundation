<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Listings;

use Kabooodle\Models\User;

/**
 * Class DeleteListingItemCommand
 */
final class DeleteListingItemCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var int
     */
    public $listingId;

    /**
     * @var int
     */
    public $listingItemId;

    /**
     * @param User $actor
     * @param int  $listingId
     * @param int  $listingItemId
     */
    public function __construct(User $actor, int $listingId, int $listingItemId)
    {
        $this->actor = $actor;
        $this->listingId = $listingId;
        $this->listingItemId = $listingItemId;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
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
