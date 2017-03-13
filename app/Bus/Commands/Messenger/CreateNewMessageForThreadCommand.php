<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Messenger;

use Kabooodle\Models\User;
use Kabooodle\Models\Threads;

/**
 * Class CreateNewMessageForThreadCommand
 */
final class CreateNewMessageForThreadCommand
{
    /**
     * @var Threads
     */
    public $thread;

    /**
     * @var User
     */
    public $author;

    /**
     * @var string
     */
    public $message;

    /**
     * @param Threads $threadId
     * @param User    $author
     * @param string  $message
     */
    public function __construct(Threads $threadId, User $author, string $message)
    {
        $this->threadId = $threadId;
        $this->author = $author;
        $this->message = $message;
    }

    /**
     * @return Threads
     */
    public function getThread(): Threads
    {
        return $this->threadId;
    }

    /**
     * @return User
     */
    public function getAuthor(): User
    {
        return $this->author;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
