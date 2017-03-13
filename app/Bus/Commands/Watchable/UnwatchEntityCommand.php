<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Watchable;

use Kabooodle\Models\User;
use Kabooodle\Models\Contracts\WatchableInterface;

/**
 * Class UnwatchEntityCommand
 */
final class UnwatchEntityCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var WatchableInterface
     */
    public $watchable;

    /**
     * @param User               $actor
     * @param WatchableInterface $watchable
     */
    public function __construct(User $actor, WatchableInterface $watchable)
    {
        $this->actor = $actor;
        $this->watchable = $watchable;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return WatchableInterface
     */
    public function getWatchable(): WatchableInterface
    {
        return $this->watchable;
    }
}
