<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Address;

use Kabooodle\Bus\Commands\Address\AddAddressCommand;
use Kabooodle\Models\Address;
use DB;

/**
 * Class AddAddressCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\User
 */
class AddAddressCommandHandler
{
    /**
     * @param AddAddressCommand $command
     *
     * @return Address
     */
    public function handle(AddAddressCommand $command)
    {
        return DB::transaction(function() use ($command) {

            $actor = $command->getUser();

            $address = Address::factory([
                'user_id' => $command->getUser()->id,
                'object_id' => $command->getObjectId(),
                'type' => $command->getType(),
                'primary' => $command->isPrimary(),
                'name' => $command->getName(),
                'company' => $command->getCompany() ?: null,
                'street1' => $command->getStreet1(),
                'street2' => $command->getStreet2() ?: null,
                'city' => $command->getCity(),
                'state' => $command->getState(),
                'zip' => $command->getZip(),
                'phone' => $command->getPhone() ?: null,
                'metadata' => $command->getMetadata(),
            ]);

            if ($address->isPrimary() || $actor->addresses->count() == 1) {
                $address->user->makeAddressOnlyPrimary($address);
            }

            return $address;
        });
    }
}
