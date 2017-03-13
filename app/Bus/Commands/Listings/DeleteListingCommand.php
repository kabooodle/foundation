<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Listings;

use Kabooodle\Models\User;

/**
 * Class DeleteListingCommand
 */
final class DeleteListingCommand
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
     * @param User $actor
     * @param      $listingId
     */
    public function __construct(User $actor, int $listingId)
    {
        $this->actor = $actor;
        $this->listingId = $listingId;
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
}
