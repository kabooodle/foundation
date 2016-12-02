<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
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
     * @var User
     */
    public $actor;

    /**
     * @var Listings
     */
    public $listing;

    /**
     * @param User     $actor
     * @param Listings $listing
     */
    public function __construct(User $actor, Listings $listing)
    {
        $this->actor = $actor;
        $this->listing = $listing;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return Listings
     */
    public function getListing(): Listings
    {
        return $this->listing;
    }
}
