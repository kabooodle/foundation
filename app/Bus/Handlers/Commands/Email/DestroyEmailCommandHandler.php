<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Email;

use Kabooodle\Bus\Commands\Email\DestroyEmailCommand;
use Kabooodle\Models\Email;

/**
 * Class DestroyEmailCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\User
 */
class DestroyEmailCommandHandler
{
    /**
     * @param DestroyEmailCommand $command
     *
     * @return boolean
     */
    public function handle(DestroyEmailCommand $command)
    {
        $email = $command->getEmail();
        $email->delete();
        return !is_null($email->deleted_at);
    }
}
