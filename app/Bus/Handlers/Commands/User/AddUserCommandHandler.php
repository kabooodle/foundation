<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\User;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Notifications\GetActiveNotifications;
use Kabooodle\Bus\Events\Email\EmailWasCreatedEvent;
use Kabooodle\Models\Email;
use Kabooodle\Models\Notifications;
use Kabooodle\Models\User;
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
     * AddUserCommandHandler constructor.
     *
     * @param User $user
     * @param Email $email
     */
    public function __construct(User $user, Email $email)
    {
        $this->user = $user;
        $this->email = $email;
    }

    /**
     * @param AddUserCommand $command
     *
     * @return User
     */
    public function handle(AddUserCommand $command)
    {
        $user = $this->user;
        $user = $user::factory([
            'first_name' => $command->getFirstName(),
            'last_name' => $command->getLastName(),
            'username' => $command->getUsername(),
            'password' => bcrypt($command->getPassword()),
            'referred_by_user_id' => $command->getReferralId()
        ]);

        $email = $this->email;
        $email = $email::factory([
            'user_id' => $user->id,
            'address' => $command->getEmail(),
            'primary' => true,
            'verified' => false,
        ]);

        $notifications = $this->dispatchNow(new GetActiveNotifications);
        $user->notificationsettings()->saveMany($notifications);

        event(new UserWasCreatedEvent($user));
        event(new EmailWasCreatedEvent($email));

        return $user;
    }
}
