<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Social;

use Kabooodle\Models\User;
use Kabooodle\Bus\Events\Event;

/**
 * Class UserFacebookCredentialsRevokedEvent
 * @package Kabooodle\Bus\Events\Social
 */
final class UserFacebookCredentialsRevokedEvent extends Event
{
    /**
     * @var User
     */
    private $actor;

    /**
     * UserFacebookCredentialsRevokedEvent constructor.
     *
     * @param User $actor
     */
    public function __construct(User $actor)
    {
        $this->actor= $actor;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }
}
