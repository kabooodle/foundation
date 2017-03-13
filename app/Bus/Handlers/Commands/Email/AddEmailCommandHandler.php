<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Email;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Email\AddEmailCommand;
use Kabooodle\Bus\Events\Email\EmailWasCreatedEvent;
use Kabooodle\Models\Email;
use DB;

/**
 * Class AddEmailCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\User
 */
class AddEmailCommandHandler
{
    use DispatchesJobs;

    /**
     * @param AddEmailCommand $command
     *
     * @return Email
     */
    public function handle(AddEmailCommand $command)
    {
        return DB::transaction(function() use ($command) {
            $email = Email::factory([
                'user_id' => $command->getUser()->id,
                'address' => $command->getAddress(),
                'primary' => $command->isPrimary(),
                'verified' => false,
            ]);

            if ($email->isPrimary()) {
                $email->user->makeEmailOnlyPrimary($email);
            }

            event(new EmailWasCreatedEvent($email));

            return $email;
        });
    }
}
