<?php

namespace Kabooodle\Bus\Handlers\Commands\User;

use Kabooodle\Models\User;
use Kabooodle\Bus\Events\User\UserWasCreatedEvent;
use Kabooodle\Bus\Commands\User\AddUserCommand;

/**
 * Class AddUserCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\User
 */
class AddUserCommandHandler
{
    /**
     * @param AddUserCommand $command
     *
     * @return User
     */
    public function handle(AddUserCommand $command)
    {
        $user = User::factory([
            'name' => $command->getName(),
            'email' => $command->getEmail(),
            'password' => bcrypt($command->getPassword())
        ]);

        event(new UserWasCreatedEvent($user));

        return $user;
    }
}