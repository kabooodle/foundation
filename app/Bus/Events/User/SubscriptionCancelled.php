<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\User;

use Kabooodle\Models\User;

/**
 * Class SubscriptionCancelled
 */
final class SubscriptionCancelled
{
    /**
     * @var User
     */
    public $subscription;

    /**
     * @param User $subscription
     */
    public function __construct($subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * @return User
     */
    public function getSubscription()
    {
        return $this->subscription;
    }
}