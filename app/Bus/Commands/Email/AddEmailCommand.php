<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Email;

use Kabooodle\Models\User;

/**
 * Class AddEmailCommand
 * @package Kabooodle\Bus\Commands\User
 */
final class AddEmailCommand
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $address;

    /**
     * @var bool
     */
    public $primary;

    /**
     * AddEmailCommand constructor.
     * @param User $user
     * @param $address
     * @param $primary
     */
    public function __construct(User $user, $address, $primary)
    {
        $this->user = $user;
        $this->address = $address;
        $this->primary = $primary;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function isPrimary()
    {
        return (bool) $this->primary;
    }
}
