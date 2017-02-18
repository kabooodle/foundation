<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Listings;

use Kabooodle\Models\User;
use Kabooodle\Models\Listing\FacebookListingOptions;

/**
 * Class ScheduleListingsCommand
 */
final class ScheduleFacebookListingCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var FacebookListingOptions
     */
    public $facebookListingOptions;

    /**
     * @param User                   $actor
     * @param FacebookListingOptions $facebookListingOptions
     */
    public function __construct(
        User $actor,
        FacebookListingOptions $facebookListingOptions
    )
    {
        $this->actor = $actor;
        $this->facebookListingOptions = $facebookListingOptions;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return FacebookListingOptions
     */
    public function getFacebookListingOptions(): FacebookListingOptions
    {
        return $this->facebookListingOptions;
    }
}
