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
 * Class MessageWasAddedToThreadEvent
 */
final class MessageWasAddedToThreadEvent
{
    /**
     * @var Threads
     */
    public $thread;

    /**
     * @var ThreadMessages
     */
    public $message;

    /**
     * @var User
     */
    public $author;

    /**
     * @param Threads        $thread
     * @param ThreadMessages $message
     * @param User           $author
     */
    public function __construct(Threads $thread, ThreadMessages $message, User $author)
    {
        $this->thread = $thread;
        $this->message = $message;
        $this->author = $author;
    }

    /**
     * @return Threads
     */
    public function getThread(): Threads
    {
        return $this->thread;
    }

    /**
     * @return ThreadMessages
     */
    public function getMessage(): ThreadMessages
    {
        return $this->message;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }
}
