<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Email;

use Kabooodle\Bus\Commands\Email\ResendEmailVerificationCommand;
use Kabooodle\Libraries\Emails\PiperEmail;
use Kabooodle\Models\Email;

/**
 * Class ResendEmailVerificationCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\User
 */
class ResendEmailVerificationCommandHandler
{


    protected $mail;

    /**
     * ResendEmailVerificationCommandHandler constructor.
     * @param PiperEmail $mail
     */
    public function __construct(PiperEmail $mail)
    {
        $this->mail = $mail;
    }

    /**
     * @param ResendEmailVerificationCommand $command
     *
     * @return Email
     */
    public function handle(ResendEmailVerificationCommand $command)
    {
        $email = $command->getEmail();
        $email->generateNewToken();
        $this->mail->sendEmailVerificationEmail($email);
    }
}
