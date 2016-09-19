<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Profile;

use Kabooodle\Models\User;

/**
 * Class SubscribeUserToPlanCommand
 * @package Kabooodle\Bus\Commands\Profile
 */
class SubscribeUserToPlanCommand
{
    /**
     * @var User
     */
    private $actor;

    /**
     * @var string
     */
    private $subscription;

    /**
     * @var string
     */
    private $plan;

    /**
     * @var bool
     */
    private $skipTrial;

    /**
     * @var int
     */
    private $trialDays;

    /**
     * SubscribeUserToPlanCommand constructor.
     *
     * @param User $actor
     * @param      $subscriptionName
     * @param      $planName
     * @param bool $skipTrial
     * @param int  $trialDays
     */
    public function __construct(User $actor, $subscriptionName, $planName, $skipTrial = false, $trialDays = 30)
    {
        $this->actor = $actor;
        $this->subscription = $subscriptionName;
        $this->plan = $planName;
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
        return $this->subscription;
    }

    /**
     * @return mixed
     */
    public function getPlanId()
    {
        return $this->plan;
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