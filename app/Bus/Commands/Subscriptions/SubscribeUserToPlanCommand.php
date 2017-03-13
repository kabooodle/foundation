<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Subscriptions;

use Kabooodle\Models\User;

/**
 * Class SubscribeUserToPlanCommand
 */
final class SubscribeUserToPlanCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var string
     */
    public $subscriptionName;

    /**
     * @var int
     */
    public $planId;

    /**
     * @var bool
     */
    public $skipTrial;

    /**
     * @var int
     */
    public $trialDays;

    /**
     * SubscribeUserToPlanCommand constructor.
     *
     * @param User $actor
     * @param      $subscriptionName
     * @param int     $planId
     * @param bool $skipTrial
     * @param int  $trialDays
     */
    public function __construct(User $actor, $subscriptionName, $planId, $skipTrial = true, $trialDays = 30)
    {
        $this->actor = $actor;
        $this->subscriptionName = $subscriptionName;
        $this->planId = $planId;
        $this->skipTrial = $skipTrial;
        $this->trialDays = $trialDays;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @return mixed
     */
    public function getSubscriptionName()
    {
        return $this->subscriptionName;
    }

    /**
     * @return mixed
     */
    public function getPlanId()
    {
        return $this->planId;
    }

    /**
     * @return bool
     */
    public function skipTrial()
    {
        return $this->skipTrial;
    }

    /**
     * @return int
     */
    public function getTrialDays()
    {
        return $this->trialDays;
    }
}
