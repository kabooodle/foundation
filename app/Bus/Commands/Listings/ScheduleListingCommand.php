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
     * @var bool
     */
    public $includeDescrText;

    /**
     * @var null
     */
    public $scheduledFor;

    /**
     * @var null
     */
    public $availableAt;

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
     * @var array
     */
    public $selectedItems;

    /**
     * @param User     $actor
     * @param bool     $includeDescrText
     * @param null     $scheduledFor
     * @param null     $availableAt
     * @param int|null $flashSaleId
     * @param array    $facebookAlbums
     * @param int|null $facebookGroupId
     * @param array    $selectedItems
     */
    public function __construct(
        User $actor,
        $includeDescrText = true,
        $scheduledFor = null,
        $availableAt = null,
        int $flashSaleId = null,
        array $facebookAlbums = [],
        int $facebookGroupId = null,
        array $selectedItems = []
    )
    {
        $this->actor = $actor;
        $this->includeDescrText = $includeDescrText;
        $this->scheduledFor = $scheduledFor;
        $this->availableAt = $availableAt;
        $this->flashSaleId = $flashSaleId;
        $this->facebookAlbums = $facebookAlbums;
        $this->facebookGroupId = $facebookGroupId;
        $this->selectedItems = $selectedItems;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return bool
     */
    public function includeDescrText(): bool
    {
        return $this->includeDescrText;
    }

    /**
     * @return boolean
     */
    public function isIncludeDescrText(): bool
    {
        return $this->includeDescrText;
    }

    /**
     * @return null
     */
    public function getScheduledFor()
    {
        return $this->scheduledFor;
    }

    /**
     * @return null
     */
    public function getAvailableAt()
    {
        return $this->availableAt;
    }

    /**
     * @return int
     */
    public function getFlashSaleId(): int
    {
        return $this->flashSaleId;
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
}
