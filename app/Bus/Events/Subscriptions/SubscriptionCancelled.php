<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Subscriptions;

use Kabooodle\Models\User;
use Laravel\Cashier\Subscription;

/**
 * Class SubscriptionCancelled
 */
final class SubscriptionCancelled
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var Subscription
     */
    public $subscription;

    /**
     * @var array
     */
    public $stripePayload;

    /**
     * @param User         $user
     * @param Subscription $subscription
     * @param array        $stripePayload
     */
    public function __construct(User $user, Subscription $subscription, array $stripePayload)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->stripePayload = $stripePayload;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Subscription
     */
    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    /**
     * @return array
     */
    public function getStripePayload(): array
    {
        return $this->stripePayload;
    }
}
