<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Social\Facebook;

use Kabooodle\Models\User;

/**
 * Class RefreshUserFacebookGroupsCommand
 * @package Kabooodle\Bus\Commands\Social\Facebook
 */
class RefreshUserFacebookGroupsCommand
{
    /**
     * RefreshUserFacebookGroupsCommand constructor.
     *
     * @param User $actor
     */
    public function __construct(User $actor)
    {
        $this->actor = $actor;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }
}
