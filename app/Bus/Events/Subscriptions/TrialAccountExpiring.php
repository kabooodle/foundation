<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Subscriptions;

use Kabooodle\Models\User;

/**
 * Class TrialAccountExpiring
 */
final class TrialAccountExpiring
{
    /**
     * @var User
     */
    public $accountHolder;

    /**
     * @var int
     */
    public $daysInterval;

    /**
     * @param User $accountHolder
     * @param int  $daysInterval
     */
    public function __construct(User $accountHolder, int $daysInterval)
    {
        $this->accountHolder = $accountHolder;
        $this->daysInterval = $daysInterval;
    }

    /**
     * @return User
     */
    public function getAccountHolder(): User
    {
        return $this->accountHolder;
    }

    /**
     * @return int
     */
    public function getDaysInterval(): int
    {
        return $this->daysInterval;
    }
}
