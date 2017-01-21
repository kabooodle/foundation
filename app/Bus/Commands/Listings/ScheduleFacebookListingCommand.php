<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
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
     * @var array
     */
    public $facebookAlbums;

    /**
     * @var int
     */
    public $facebookGroupId;

    /**
     * @var array
     */
    public $selectedItems;

    /**
     * @var FacebookListingOptions
     */
    public $facebookListingOptions;

    /**
     * @param User                   $actor
     * @param array                  $facebookAlbums
     * @param int|null               $facebookGroupId
     * @param array                  $selectedItems
     * @param FacebookListingOptions $facebookListingOptions
     */
    public function __construct(
        User $actor,
        array $facebookAlbums = [],
        int $facebookGroupId = null,
        array $selectedItems = [],
        FacebookListingOptions $facebookListingOptions
    )
    {
        $this->actor = $actor;
        $this->facebookAlbums = $facebookAlbums;
        $this->facebookGroupId = $facebookGroupId;
        $this->selectedItems = $selectedItems;
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
     * @return array
     */
    public function getFacebookAlbums(): array
    {
        return $this->facebookAlbums;
    }

    /**
     * @return int
     */
    public function getFacebookGroupId()
    {
        return $this->facebookGroupId;
    }

    /**
     * @return array
     */
    public function getSelectedItems(): array
    {
        return $this->selectedItems;
    }

    /**
     * @return FacebookListingOptions
     */
    public function getFacebookListingOptions(): FacebookListingOptions
    {
        return $this->facebookListingOptions;
    }
}
