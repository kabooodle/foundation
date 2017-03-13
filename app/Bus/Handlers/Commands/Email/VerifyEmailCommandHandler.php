<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Email;

use Kabooodle\Bus\Commands\Email\VerifyEmailCommand;
use Kabooodle\Bus\Events\Email\EmailWasVerifiedEvent;
use Kabooodle\Models\Email;

/**
 * Class VerifyEmailCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\User
 */
class VerifyEmailCommandHandler
{
    protected $email;

    /**
     * VerifyEmailCommandHandler constructor.
     * @param Email $email
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * @param VerifyEmailCommand $command
     *
     * @return Email
     */
    public function handle(VerifyEmailCommand $command)
    {
        $email = $this->email->whereToken($command->getToken())->firstOrFail()->verify();

        if ($email) {
            event(new EmailWasVerifiedEvent($email));
        }
    }
}
