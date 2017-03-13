<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\User;

use Kabooodle\Models\User;
use Laravel\Cashier\Subscription;

/**
 * Class ReferralHasQualified
 */
final class ReferralHasQualified
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var User
     */
    public $referredBy;

    /**
     * @var Subscription
     */
    public $subscription;

    /**
     * @var string
     */
    public $plan;

    /**
     * ReferralHasQualified constructor.
     * @param User $actor
     * @param User $referredBy
     * @param Subscription $subscription
     * @param string $plan
     */
    public function __construct(User $actor, User $referredBy, Subscription $subscription, string $plan)
    {
        $this->actor = $actor;
        $this->referredBy = $referredBy;
        $this->subscription = $subscription;
        $this->plan = $plan;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return User
     */
    public function getReferredBy(): User
    {
        return $this->referredBy;
    }

    /**
     * @return Subscription
     */
    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    /**
     * @return string
     */
    public function getPlan(): string
    {
        return $this->plan;
    }
}
