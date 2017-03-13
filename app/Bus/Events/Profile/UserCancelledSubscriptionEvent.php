<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Profile;

use Kabooodle\Models\User;

/**
 * Class UserCancelledSubscriptionEvent
 */
final class UserCancelledSubscriptionEvent
{
    /**
     * @var User
     */
    public $subscriber;

    /**
     * @var string
     */
    public $subscriptionName;

    /**
     * @param User $subscriber
     * @param string $subscriptionName
     */
    public function __construct(User $subscriber, string $subscriptionName = 'main')
    {
        $this->subscriber = $subscriber;
        $this->subscriptionName = $subscriptionName;
    }

    /**
     * @return User
     */
    public function getSubscriber(): User
    {
        return $this->subscriber;
    }

    /**
     * @return string
     */
    public function getSubscriptionName(): string
    {
        return $this->subscriptionName;
    }
}