<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Address;

use Kabooodle\Bus\Commands\Address\MakeAddressPrimaryCommand;
use Kabooodle\Models\Address;

/**
 * Class MakeAddressPrimaryCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\User
 */
class MakeAddressPrimaryCommandHandler
{
    protected $address;

    /**
     * MakeAddressPrimaryCommandHandler constructor.
     * @param Address $address
     */
    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    /**
     * @param MakeAddressPrimaryCommand $command
     *
     * @return boolean
     */
    public function handle(MakeAddressPrimaryCommand $command)
    {
        $address = $command->getAddress();
        $address->primary = true;
        $address->save();

        $address->user->makeAddressOnlyPrimary($address);
        return $address->isPrimary();
    }
}
