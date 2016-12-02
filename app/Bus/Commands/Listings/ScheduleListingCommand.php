<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Listings;

use Kabooodle\Models\User;
use Kabooodle\Models\Listings;

/**
 * Class ScheduleListingsCommand
 */
final class ScheduleListingCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var string
     */
    public $type;

    /**
     * @var null
     */
    public $scheduledFor;

    /**
     * @var int
     */
    public $flashSaleId;

    /**
     * @var array
     */
    public $facebookAlbums;

    /**
     * @var int
     */
    public $facebookGroupId;

    /**
     * @param User $actor
     * @param string $type
     * @param null $scheduledFor
     * @param int $flashSaleId
     * @param array $facebookAlbums
     * @param int|null  $facebookGroupId
     */
    public function __construct(
        User $actor,
        $type = Listings::TYPE_FACEBOOK,
        $scheduledFor = null,
        int $flashSaleId = null,
        array $facebookAlbums = [],
        int $facebookGroupId = null
    )
    {
        $this->actor = $actor;
        $this->scheduledFor = $scheduledFor;
        $this->type = $type;
        $this->flashSaleId = $flashSaleId;
        $this->facebookAlbums = $facebookAlbums;
        $this->facebookGroupId = $facebookGroupId;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return null
     */
    public function getScheduledFor()
    {
        return $this->scheduledFor;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array|null
     */
    public function getFacebookAlbums()
    {
        return $this->facebookAlbums;
    }

    /**
     * @return int|null
     */
    public function getFlashSaleId()
    {
        return $this->flashSaleId;
    }

    /**
     * @return int|null
     */
    public function getFacebookGroupId()
    {
        return $this->facebookGroupId;
    }
}
