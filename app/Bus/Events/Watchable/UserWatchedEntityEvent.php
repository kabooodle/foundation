<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Watchable;

use Kabooodle\Models\User;
use Kabooodle\Models\Watches;
use Kabooodle\Models\Contracts\WatchableInterface;

/**
 * Class UserWatchedEntityEvent
 */
final class UserWatchedEntityEvent
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var Watches
     */
    public $watch;

    /**
     * @var WatchableInterface
     */
    public $watchable;

    /**
     * @param User               $user
     * @param Watches            $watch
     * @param WatchableInterface $watchable
     */
    public function __construct(User $user, Watches $watch, WatchableInterface $watchable)
    {
        $this->user = $user;
        $this->watch = $watch;
        $this->watchable = $watchable;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Watches
     */
    public function getWatch(): Watches
    {
        return $this->watch;
    }

    /**
     * @return WatchableInterface
     */
    public function getWatchable(): WatchableInterface
    {
        return $this->watchable;
    }
}