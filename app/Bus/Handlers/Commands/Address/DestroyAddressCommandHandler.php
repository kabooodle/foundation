<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Address;

use Kabooodle\Bus\Commands\Address\DestroyAddressCommand;

/**
 * Class DestroyAddressCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\User
 */
class DestroyAddressCommandHandler
{
    /**
     * @param DestroyAddressCommand $command
     *
     * @return boolean
     */
    public function handle(DestroyAddressCommand $command)
    {
        $address = $command->getAddress();
        $address->delete();
        return !is_null($address->deleted_at);
    }
}
