<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Messenger;

use Kabooodle\Models\User;
use Cmgmyr\Messenger\Models\Thread;
use Kabooodle\Models\ThreadMessages;
use Cmgmyr\Messenger\Models\Message;

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
     * @var ThreadMessages
     */
    public $message;

    /**
     * @param Thread         $thread
     * @param User           $sender
     * @param ThreadMessages $message
     */
    public function __construct(Thread $thread, User $sender, ThreadMessages $message)
    {
        $this->thread = $thread;
        $this->sender = $sender;
        $this->message = $message;
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

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }
}
