<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\User;

use DB;
use Kabooodle\Models\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\User\AddUserCommand;
use Kabooodle\Bus\Events\User\UserWasCreatedEvent;

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
                'name' => $command->getName(),
                'email' => $command->getEmail(),
                'password' => bcrypt($command->getPassword()),
                'referred_by_user_id' => $command->getReferralId(),
            ]);

            event(new UserWasCreatedEvent($user));

            return $user;
        });
    }
}
