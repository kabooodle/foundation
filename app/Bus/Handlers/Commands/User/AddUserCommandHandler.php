<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\User;

use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\User\AddUserCommand;
use Kabooodle\Bus\Events\Email\EmailWasCreatedEvent;
use Kabooodle\Bus\Events\User\UserWasCreatedEvent;
use Kabooodle\Models\Email;
use Kabooodle\Models\User;
use Kabooodle\Services\User\UserService;

/**
 * Class AddUserCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\User
 */
class AddUserCommandHandler
{
    use DispatchesJobs;

    /**
     * @var UserService
     */
    public $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param AddUserCommand $command
     *
     * @return User
     */
    public function handle(AddUserCommand $command)
    {
        return DB::transaction(function () use ($command) {

            $referral = $command->getReferralUsername() ? $this->lookupReferralByUsername($command->getReferralUsername()) : null;

            $user = User::factory([
                'first_name' => $command->getFirstName(),
                'last_name' => $command->getLastName(),
                'username' => $command->getUsername(),
                'password' => bcrypt($command->getPassword()),
                'referred_by_user_id' => $referral ? $referral->id : null,
                'timezone' => $command->getTimezone() ? : 'America/Los_Angeles'
            ]);

            $email = Email::factory([
                'user_id' => $user->id,
                'address' => $command->getEmail(),
                'primary' => true,
                'verified' => false,
            ]);

            event(new UserWasCreatedEvent($user, $command->getAccountType()));

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
