<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
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
use Kabooodle\Services\User\UserService;

/**
 * Class ConvertGuestToUserCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\User
 */
class ConvertGuestToUserCommandHandler
{
    use DispatchesJobs;

    /**
     * @var UserService
     */
    public $userService;

    /**
     * ConvertGuestToUserCommandHandler constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param ConvertGuestToUserCommand $command
     *
     * @return User
     */
    public function handle(ConvertGuestToUserCommand $command)
    {
        return DB::transaction(function () use ($command) {
            $referral = $command->getReferralUsername() ? $this->lookupReferralByUsername($command->getReferralUsername()) : null;
            $email = $command->getEmail();

            $user = $command->getGuest();
            $user->first_name = $command->getFirstName();
            $user->last_name = $command->getLastName();
            $user->username = $command->getUsername();
            $user->password = bcrypt($command->getPassword());
            $user->referred_by_user_id = $referral ? $referral->id : null;
            $user->guest = false;
            $user->activated = $email->isVerified();
            $user->save();

            $email->primary = true;
            $email->verified = false;
            $email->save();

            $user->makeEmailOnlyPrimary($email);

            $notifications = $this->dispatchNow(new GetActiveNotifications);
            $user->notificationsettings()->saveMany($notifications);

            event(new UserWasCreatedEvent($user, 'basic'));
            event(new EmailWasCreatedEvent($email));

            return $user;
        });
    }

    /**
     * @param string|null $username
     *
     * @return mixed
     */
    public function lookupReferralByUsername(string $username = null)
    {
        $referral = $this->userService->repository->getByUsername($username);

        return $referral;
    }
}
