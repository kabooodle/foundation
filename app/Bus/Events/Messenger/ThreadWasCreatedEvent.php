<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Messenger;

use Kabooodle\Models\User;
use Kabooodle\Models\Threads;
use Kabooodle\Models\ThreadMessages;

/**
 * Class ThreadWasCreatedEvent
 */
final class ThreadWasCreatedEvent
{
    /**
     * @var Threads
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
     * @param Threads        $thread
     * @param User           $sender
     * @param ThreadMessages $message
     */
    public function __construct(Threads $thread, User $sender, ThreadMessages $message)
    {
        $this->thread = $thread;
        $this->sender = $sender;
        $this->message = $message;
    }

    /**
     * @return Threads
     */
    public function getThread(): Threads
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
     * @return ThreadMessages
     */
    public function getMessage(): ThreadMessages
    {
        return $this->message;
    }
}
