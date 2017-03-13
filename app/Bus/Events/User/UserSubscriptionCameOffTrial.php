<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\User;

use Kabooodle\Models\User;
use Kabooodle\Bus\Events\Event;

/**
 * Class UserLoggedInEvent
 * @package Kabooodle\Bus\Events\User
 */
class UserSubscriptionCameOffTrial extends Event
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var array
     */
    private $payload;

    /**
     * UserWasCreatedEvent constructor.
     *
     * @param User $user
     */
    public function __construct(User $user, array $payload)
    {
        $this->user = $user;
        $this->payload = $payload;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
