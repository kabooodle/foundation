<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Messenger;

use Kabooodle\Models\User;
use Kabooodle\Models\Messenger\MessageRecipients;

/**
 * Class CreateNewThreadCommand
 */
final class CreateNewThreadCommand
{
    /**
     * @var User
     */
    public $sender;

    /**
     * @var array
     */
    public $recipientIds;

    /**
     * @var string
     */
    public $subject;

    /**
     * @var string
     */
    public $message;

    /**
     * @param User              $sender
     * @param array             $recipientIds
     * @param string            $subject
     * @param string            $message
     */
    public function __construct(User $sender, array $recipientIds, string $subject, string $message)
    {
        $this->sender = $sender;
        $this->recipientIds = $recipientIds;
        $this->subject = $subject;
        $this->message = $message;
    }

    /**
     * @return User
     */
    public function getSender(): User
    {
        return $this->sender;
    }

    /**
     * @return array
     */
    public function getRecipientIds(): array
    {
        return $this->recipientIds;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
