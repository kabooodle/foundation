<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Messenger;

use Kabooodle\Models\User;
use Cmgmyr\Messenger\Models\Thread;

/**
 * Class ThreadWasCreatedEvent
 */
final class ThreadWasCreatedEvent
{
    /**
     * @var Thread
     */
    public $thread;

    /**
     * @var User
     */
    public $sender;

    /**
     * @param Thread $thread
     * @param User   $sender
     */
    public function __construct(Thread $thread, User $sender)
    {
        $this->thread = $thread;
        $this->sender = $sender;
    }

    /**
     * @return Thread
     */
    public function getThread(): Thread
    {
        return $this->thread;
    }

    /**
     * @return User
     */
    public function getSender(): User
    {
        return $this->sender;
    }
}
