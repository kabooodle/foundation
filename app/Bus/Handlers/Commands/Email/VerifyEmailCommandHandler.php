<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\User;

use Kabooodle\Bus\Commands\Email\VerifyEmailCommand;
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
        return $this->email->whereToken($command->getToken())->firstOrFail()->verify();
    }
}
