<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
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
            $address = Address::factory([
                'user_id' => $command->getUser()->id,
                'type' => $command->getType(),
                'primary' => $command->isPrimary(),
                'company' => $command->getCompany(),
                'street1' => $command->getStreet1(),
                'street2' => $command->getStreet2(),
                'city' => $command->getCity(),
                'state' => $command->getState(),
                'zip' => $command->getZip(),
                'phone' => $command->getPhone(),
            ]);

            if ($address->isPrimary()) {
                $address->user->makeAddressOnlyPrimary($address);
            }

            return $address;
        });
    }
}
