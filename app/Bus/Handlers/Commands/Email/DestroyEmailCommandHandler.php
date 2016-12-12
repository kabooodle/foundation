<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\User;

use Kabooodle\Bus\Commands\Email\DestroyEmailCommand;
use Kabooodle\Models\Email;

/**
 * Class DestroyEmailCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\User
 */
class DestroyEmailCommandHandler
{
    protected $email;

    /**
     * DestroyEmailCommandHandler constructor.
     * @param Email $email
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * @param DestroyEmailCommand $command
     *
     * @return Email
     */
    public function handle(DestroyEmailCommand $command)
    {
        $email = $command->getEmail();
        $email->delete();
        return !is_null($email->deleted_at);
    }
}
