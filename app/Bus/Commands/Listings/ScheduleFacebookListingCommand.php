<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
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
     * @var array
     */
    public $facebookSales;

    /**
     * @param User                   $actor
     * @param FacebookListingOptions $facebookListingOptions
     * @param array                  $facebookSales
     */
    public function __construct(
        User $actor,
        FacebookListingOptions $facebookListingOptions,
        array $facebookSales
    )
    {
        $this->actor = $actor;
        $this->facebookListingOptions = $facebookListingOptions;
        $this->facebookSales = $facebookSales;
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

    /**
     * @return array
     */
    public function getFacebookSales(): array
    {
        return $this->facebookSales;
    }
}
