<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Flashsale;

use Carbon\Carbon;
use Kabooodle\Models\FlashSales;
use Kabooodle\Models\User;
use Kabooodle\Models\Groups;
use Kabooodle\Models\Dates\StartsAndEndsAt;

/**
 * Class AddFlashsaleCommand
 * @package Kabooodle\Bus\Commands\Flashsale
 */
class AddFlashsaleCommand
{
    /**
     * AddFlashsaleCommand constructor.
     *
     * @param User            $user
     * @param                 $name
     * @param                 $description
     * @param StartsAndEndsAt $startsAndEndsAt
     * @param string          $saleType
     * @param                 $hostId
     * @param string          $sellerRules
     * @param array           $adminIds
     * @param array           $sellerIds
     */
    public function __construct(User $user, $name, $description, StartsAndEndsAt $startsAndEndsAt, $saleType = FlashSales::TYPE_SINGLE, $hostId, $sellerRules = '', array $adminIds = [], array $sellerIds = [])
    {
        $this->user = $user;
        $this->name = $name;
        $this->description = $description;
        $this->startTime = $startsAndEndsAt->getStartsAt();
        $this->endTime = $startsAndEndsAt->getEndsAt();
        $this->saleType = $saleType;
        $this->hostId = $hostId;
        $this->sellerRules = $sellerRules;
        $this->adminIds = $adminIds;
        $this->sellerIds = $sellerIds;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->saleType;
    }

    /**
     * @return array
     */
    public function getAdminIds()
    {
        return $this->adminIds;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return Carbon
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @return Groups
     */
    public function getHostId()
    {
        return $this->hostId;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Carbon
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return array
     */
    public function getSellerIds()
    {
        return $this->sellerIds;
    }

    /**
     * @return string
     */
    public function getSellerRules()
    {
        return $this->sellerRules;
    }
}
