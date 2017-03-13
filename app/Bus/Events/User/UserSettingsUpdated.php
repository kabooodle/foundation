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
class UserSettingsUpdated extends Event
{
    /**
     * @var User
     */
    private $user;

    /**
     * UserWasCreatedEvent constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
