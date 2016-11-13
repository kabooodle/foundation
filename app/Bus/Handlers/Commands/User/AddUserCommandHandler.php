<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\User;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Notifications\GetActiveNotifications;
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
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
            'name' => $command->getName(),
            'email' => $command->getEmail(),
            'password' => bcrypt($command->getPassword()),
            'referred_by_user_id' => $command->getReferralId()
        ]);

        $notifications = $this->dispatchNow(new GetActiveNotifications);
        $user->notificationsettings()->saveMany($notifications);

        event(new UserWasCreatedEvent($user));

        return $user;
    }
}
