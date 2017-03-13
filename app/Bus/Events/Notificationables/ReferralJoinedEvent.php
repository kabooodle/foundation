<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Events\Notificationables;

use Kabooodle\Models\User;

/**
 * Class ReferralJoinedEvent
 * @package Kabooodle\Events\Notificationables
 */
final class ReferralJoinedEvent extends AbstractNotificationable
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var User
     */
    public $referee;

    /**
     * ReferralJoinedEvent constructor.
     *
     * @param User $user
     * @param User $referee
     */
    public function __construct(User $user, User $referee)
    {
        $this->user = $user;
        $this->referee = $referee;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return User
     */
    public function getReferee(): User
    {
        return $this->referee;
    }
}