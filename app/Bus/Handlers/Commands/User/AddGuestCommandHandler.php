<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\User;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\User\AddGuestCommand;
use Kabooodle\Models\Email;
use Kabooodle\Models\User;

/**
 * Class AddUserCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\User
 */
class AddGuestCommandHandler
{
    /**
     * AddGuestCommandHandler constructor.
     * @param User $user
     * @param Email $email
     */
    public function __construct(User $user, Email $email)
    {
        $this->user = $user;
        $this->email = $email;
    }

    /**
     * @param AddGuestCommand $command
     *
     * @return User
     */
    public function handle(AddGuestCommand $command)
    {
        $user = $this->user;
        $guest = $user::factory([
            'first_name' => $command->getFirstName(),
            'last_name' => $command->getLastName(),
            'email' => $command->getEmail(),
            'guest' => true,
        ]);

        $email = $this->email;
        $email::factory([
            'user_id' => $guest->id,
            'address' => $command->getEmail(),
            'primary' => true,
            'verified' => false,
        ]);

        return $guest;
    }
}
