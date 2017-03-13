<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Followable;

use Kabooodle\Models\User;
use Kabooodle\Models\Contracts\WatchableInterface;

/**
 * Class FollowNewEntityCommand
 */
final class FollowNewEntityCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var WatchableInterface
     */
    public $followable;

    /**
     * @param User               $actor
     * @param WatchableInterface $watchable
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