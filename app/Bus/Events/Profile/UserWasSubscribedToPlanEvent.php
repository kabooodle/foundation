<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Profile;

use Kabooodle\Models\User;
use Laravel\Cashier\Subscription;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kabooodle\Models\Contracts\NotificationableInterface;

/**
 * Class UserWasSubscribedToPlanEvent
 * @package Kabooodle\Bus\Events\Profile
 */
final class UserWasSubscribedToPlanEvent implements NotificationableInterface, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

        /**
     * @var User
     */
    private $actor;

    /**
     * @var Subscription
     */
    private $subscription;

    /**
     * @var string
     */
    private $plan;

    /**
     * @var bool
     */
    private $poppingCherry;

    /**
     *
     */
    private $swappedPlan;

    /**
     * UserWasSubscribedToPlanEvent constructor.
     *
     * @param User          $actor
     * @param Subscription  $subscription
     * @param string        $plan
     * @param bool          $poppingCherry
     * @param bool          $swappedPlan
     */
    public function __construct(User $actor, Subscription $subscription, $plan, $poppingCherry = false, $swappedPlan = false)
    {
        $this->actor = $actor;
        $this->subscription = $subscription;
        $this->plan = $plan;
        $this->poppingCherry = $poppingCherry;
        $this->swappedPlan = $swappedPlan;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @return string
     */
    public function getPlan()
    {
        return $this->plan;
    }

    /**
     * @return boolean
     */
    public function isPoppingCherry()
    {
        return $this->poppingCherry;
    }

    /**
     * @return Subscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * @return boolean
     */
    public function isSwappedPlan()
    {
        return $this->swappedPlan;
    }
}
