<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Followable;

use Kabooodle\Models\User;

/**
 * Class UnfollowEntityCommand
 */
final class UnfollowEntityCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var
     */
    public $followable;

    /**
     * @param User               $actor
     * @param $followable
     */
    public function __construct(User $actor, $followable)
    {
        $this->actor = $actor;
        $this->followable = $followable;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return
     */
    public function getFollowable()
    {
        return $this->followable;
    }
}