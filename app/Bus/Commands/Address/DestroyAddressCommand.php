<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Address;

use Kabooodle\Models\Address;

/**
 * Class DestroyAddressCommand
 * @package Kabooodle\Bus\Commands\User
 */
final class DestroyAddressCommand
{
    /**
     * @var Address
     */
    public $address;

    /**
     * DestroyAddressCommand constructor.
     *
     * @param Address $address
     */
    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }
}
