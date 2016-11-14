<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Flashsale;

use Carbon\Carbon;
use Kabooodle\Models\User;
use Kabooodle\Models\Groups;
use Kabooodle\Models\FlashSales;
use Kabooodle\Models\Dates\StartsAndEndsAt;

/**
 * Class UpdateFlashsaleCommand
 * @package Kabooodle\Bus\Commands\Flashsale
 */
class UpdateFlashsaleCommand
{
    /**
     * UpdateFlashsaleCommand constructor.
     *
     * @param FlashSales      $flashsale
     * @param User            $user
     * @param                 $name
     * @param                 $description
     * @param StartsAndEndsAt $startsAndEndsAt
     * @param string          $sellerRules
     * @param array           $adminIds
     * @param array           $invitedSellerEmails
     * @param                 $privacy
     */
    public function __construct(FlashSales $flashsale, User $user, $name, $description, StartsAndEndsAt $startsAndEndsAt, $sellerRules = '', array $adminIds = [], array $invitedSellerEmails = [], $privacy)
    {
        $this->flashsale = $flashsale;
        $this->user = $user;
        $this->name = $name;
        $this->description = $description;
        $this->startTime = $startsAndEndsAt->getStartsAt();
        $this->endTime = $startsAndEndsAt->getEndsAt();
        $this->sellerRules = $sellerRules;
        $this->adminIds = $adminIds;
        $this->invitedSellerEmails = $invitedSellerEmails;
        $this->privacy = $privacy;
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
     * @return FlashSales
     */
    public function getFlashSale()
    {
        return $this->flashsale;
    }

    /**
     * @return mixed
     */
    public function getPrivacy()
    {
        return $this->privacy;
    }

    /**
     * @return array
     */
    public function getInvitedSellerEmails()
    {
        return $this->invitedSellerEmails;
    }

    /**
     * @return string
     */
    public function getSellerRules()
    {
        return $this->sellerRules;
    }
}
