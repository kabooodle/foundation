<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Listings;

use Kabooodle\Models\User;

/**
 * Class CheckFacebookListingsQuotaForPeriod
 */
final class CheckFacebookListingsQuotaForPeriod
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var
     */
    public $startTime;

    /**
     * @var
     */
    public $endTime;

    /**
     * @var int
     */
    public $incomingItemsCount;

    /**
     * @param User $actor
     * @param      $startTime
     * @param      $endTime
     * @param int  $incomingItemsCount
     */
    public function __construct(User $actor, $startTime, $endTime, int $incomingItemsCount)
    {
        $this->actor = $actor;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->incomingItemsCount = $incomingItemsCount;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @return int
     */
    public function getIncomingItemsCount(): int
    {
        return $this->incomingItemsCount;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }
}
