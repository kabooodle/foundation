<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Profile;

use Kabooodle\Models\User;
use Laravel\Cashier\Subscription;
use Illuminate\Queue\SerializesModels;
use Kabooodle\Models\Contracts\NotificationableInterface;

/**
 * Class UserWasSubscribedToPlanEvent
 * @package Kabooodle\Bus\Events\Profile
 */
final class UserWasSubscribedToPlanEvent implements NotificationableInterface
{
    use SerializesModels;

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
    }
}