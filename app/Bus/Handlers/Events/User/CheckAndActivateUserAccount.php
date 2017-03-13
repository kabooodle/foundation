<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\User;

use Kabooodle\Bus\Events\Email\EmailWasVerifiedEvent;

/**
 * Class CheckAndActivateUserAccount
 */
class CheckAndActivateUserAccount
{
    /**
     * @param EmailWasVerifiedEvent $event
     */
    public function handle(EmailWasVerifiedEvent $event)
    {
        $email = $event->getEmail();
        $user = $email->user;
        $user->activated = true;
        $user->save();
    }
}