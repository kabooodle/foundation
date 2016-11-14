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
final class GetUserFacebookGroupsCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * GetUserFacebookGroupsCommand constructor.
     *
     * @param User $user
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
