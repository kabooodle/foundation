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
use Kabooodle\Models\User;
use Kabooodle\Bus\Commands\User\ConvertGuestToUserCommand;
use Kabooodle\Bus\Events\User\UserWasCreatedEvent;
use DB;

/**
 * Class ConvertGuestToUserCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\User
 */
class ConvertGuestToUserCommandHandler
{
    use DispatchesJobs;

    /**
     * ConvertGuestToUserCommandHandler constructor.
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
     * @param ConvertGuestToUserCommand $command
     *
     * @return User
     */
    public function handle(ConvertGuestToUserCommand $command)
    {
        return DB::transaction(function () use ($command) {
            $user = $command->getGuest();
            $user->first_name = $command->getFirstName();
            $user->last_name = $command->getLastName();
            $user->username = $command->getUsername();
            $user->password = bcrypt($command->getPassword());
            $user->referred_by_user_id = $command->getReferralId();
            $user->guest = false;
            $user->save();

            $email = $command->getEmail();
            $email->primary = true;
            $email->verified = false;
            $email->save();

            $user->makeEmailOnlyPrimary($email);

            $notifications = $this->dispatchNow(new GetActiveNotifications);
            $user->notificationsettings()->saveMany($notifications);

            event(new UserWasCreatedEvent($user));
            event(new EmailWasCreatedEvent($email));

            return $user;
        });
    }
}
