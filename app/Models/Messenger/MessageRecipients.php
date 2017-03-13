<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Messenger;

use Kabooodle\Models\User;
use Illuminate\Support\Collection;

/**
 * Class MessageRecipients
 */
final class MessageRecipients
{
    /**
     * @var User
     */
    public $sender;

    /**
     * @var array
     */
    public $recipients;

    /**
     * @param User  $sender
     * @param array $recipientIds
     */
    public function __construct(User $sender, array $recipientIds)
    {
        $this->sender = $sender;
        $this->validateRecipients($recipientIds);
    }

    /**
     * Users can only send messages to users they are following
     * and users from whom they have previously claimed items.
     *
     * @param array $recipientIds
     */
    public function validateRecipients(array $recipientIds)
    {
        $sender = $this->sender;

        /** @var Collection $following */
//        $usersSenderFollows = $sender->following;
        $this->recipients = $recipientIds;
    }

    /**
     * @return array
     */
    public function getRecipients() : array
    {
        return $this->recipients;
    }
}