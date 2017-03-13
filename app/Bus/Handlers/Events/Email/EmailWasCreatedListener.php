<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Email;

use Kabooodle\Libraries\Emails\PiperEmail;
use Kabooodle\Bus\Events\Email\EmailWasCreatedEvent;

/**
 * Class EmailWasCreatedListener
 * @package Kabooodle\Bus\Handlers\Events\Email
 */
class EmailWasCreatedListener
{
    /**
     * EmailWasCreatedListener constructor.
     *
     * @param PiperEmail $mail
     */
    public function __construct(PiperEmail $mail)
    {
        $this->mail = $mail;
    }

    /**
     * @param EmailWasCreatedEvent $event
     */
    public function handle(EmailWasCreatedEvent $event)
    {
        $this->mail->sendEmailVerificationEmail($event->getEmail());
    }
}
