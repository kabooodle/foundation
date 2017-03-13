<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
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
     * @var bool
     */
    public $forceRefresh;

    /**
     * @param User $actor
     * @param bool $forceRefresh
     */
    public function __construct(User $actor, bool $forceRefresh = false)
    {
        $this->actor = $actor;
        $this->forceRefresh = $forceRefresh;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @return bool
     */
    public function isForcedRefresh(): bool
    {
        return $this->forceRefresh;
    }

}
