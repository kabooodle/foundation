<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\User;

use Kabooodle\Bus\Commands\User\AddGuestCommand;
use Kabooodle\Models\Address;
use Kabooodle\Models\Email;
use Kabooodle\Models\User;
use DB;

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
     * @param Address $address
     */
    public function __construct(User $user, Email $email, Address $address)
    {
        $this->user = $user;
        $this->email = $email;
        $this->address = $address;
    }

    /**
     * @param AddGuestCommand $command
     *
     * @return User
     */
    public function handle(AddGuestCommand $command)
    {
        return DB::transaction(function () use ($command) {
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

            $address = $this->address;
            $address::factory([
                'user_id' => $guest->id,
                'type' => Address::TYPE_TO,
                'primary' => 1,
                'company' => $command->getCompany(),
                'street1' => $command->getStreet1(),
                'street2' => $command->getStreet2(),
                'city' => $command->getCity(),
                'state' => $command->getState(),
                'zip' => $command->getZip(),
                'phone' => $command->getPhone(),
            ]);

            return $guest;
        });
    }
}
