<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\User;

use DB;
use Kabooodle\Models\Email;
use Kabooodle\Models\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\User\AddUserCommand;
use Kabooodle\Bus\Events\User\UserWasCreatedEvent;
use Kabooodle\Bus\Events\Email\EmailWasCreatedEvent;

/**
 * Class AddUserCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\User
 */
class AddUserCommandHandler
{
    use DispatchesJobs;

    /**
     * @param AddUserCommand $command
     *
     * @return User
     */
    public function handle(AddUserCommand $command)
    {
        return DB::transaction(function() use ($command) {
            $user = User::factory([
                'first_name' => $command->getFirstName(),
                'last_name' => $command->getLastName(),
                'username' => $command->getUsername(),
                'password' => bcrypt($command->getPassword()),
                'referred_by_user_id' => $command->getReferralId()
            ]);

            $email = Email::factory([
                'user_id' => $user->id,
                'address' => $command->getEmail(),
                'primary' => true,
                'verified' => false,
            ]);

            event(new UserWasCreatedEvent($user));
            event(new EmailWasCreatedEvent($email));

            return $user;
        });
    }
}
