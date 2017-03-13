<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Listings;

use Kabooodle\Models\User;

/**
 * Class ScheduleNewListingsCommand
 */
final class ScheduleNewListingsCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var ScheduleFacebookListingCommand|null
     */
    public $facebookListings;

    /**
     * @var ScheduleFlashsaleListingCommand|null
     */
    public $flashsaleListings;

    /**
     * @param User                                 $actor
     * @param ScheduleFacebookListingCommand|null  $facebookListings
     * @param ScheduleFlashsaleListingCommand|null $flashsaleListings
     */
    public function __construct(User $actor, ScheduleFacebookListingCommand $facebookListings = null, ScheduleFlashsaleListingCommand $flashsaleListings = null)
    {
        $this->actor = $actor;
        $this->facebookListings = $facebookListings;
        $this->flashsaleListings = $flashsaleListings;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return ScheduleFacebookListingCommand|null
     */
    public function getFacebookListings()
    {
        return $this->facebookListings;
    }

    /**
     * @return ScheduleFlashsaleListingCommand|null
     */
    public function getFlashsaleListings()
    {
        return $this->flashsaleListings;
    }
}
