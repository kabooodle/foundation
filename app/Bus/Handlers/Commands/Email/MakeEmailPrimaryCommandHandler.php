<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Email;

use Kabooodle\Bus\Commands\Email\MakeEmailPrimaryCommand;
use Kabooodle\Models\Email;

/**
 * Class MakeEmailPrimaryCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\User
 */
class MakeEmailPrimaryCommandHandler
{
    protected $email;

    /**
     * MakeEmailPrimaryCommandHandler constructor.
     * @param Email $email
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * @param MakeEmailPrimaryCommand $command
     *
     * @return boolean
     */
    public function handle(MakeEmailPrimaryCommand $command)
    {
        $email = $command->getEmail();
        $email->primary = true;
        $email->save();

        $email->user->makeEmailOnlyPrimary($email);
        return $email->isPrimary();
    }
}
