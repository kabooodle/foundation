<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Social\Facebook;

use Kabooodle\Models\User;

/**
 * Class GetUserFacebookGroupsCommand
 * @package Kabooodle\Bus\Commands\Social\Facebook
 */
class GetUserFacebookGroupsCommand
{
    /**
     * GetUserFacebookGroupsCommand constructor.
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->actor = $user;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }
}