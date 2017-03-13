<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Subscriptions;

use Kabooodle\Models\User;

/**
 * Class SubscribeUserToGenericTrialCommand
 */
final class SubscribeUserToGenericTrialCommand
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var int
     */
    public $trialDurationInDays;

    /**
     * @param User $user
     * @param int  $trialDurationInDays
     */
    public function __construct(User $user, int $trialDurationInDays = 30)
    {
        $this->user = $user;
        $this->trialDurationInDays = $trialDurationInDays;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getTrialDurationInDays(): int
    {
        return $this->trialDurationInDays;
    }
}
