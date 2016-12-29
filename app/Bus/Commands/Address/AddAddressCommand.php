<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Address;

use Kabooodle\Models\User;

/**
 * Class AddAddressCommand
 * @package Kabooodle\Bus\Commands\User
 */
final class AddAddressCommand
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var bool
     */
    protected $primary;

    /**
     * @var string
     */
    protected $fullName;

    /**
     * @var string
     */
    protected $company;

    /**
     * @var string
     */
    protected $street1;

    /**
     * @var string
     */
    protected $street2;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $zip;

    /**
     * @var string
     */
    protected $phone;

    /**
     * AddAddressCommand constructor.
     * @param User $user
     * @param $type
     * @param $primary
     * @param $fullName
     * @param $company
     * @param $street1
     * @param $street2
     * @param $city
     * @param $state
     * @param $zip
     * @param $phone
     */
    public function __construct(
        User $user,
        $type,
        $primary,
        $fullName,
        $company,
        $street1,
        $street2,
        $city,
        $state,
        $zip,
        $phone)
    {
        $this->user = $user;
        $this->type = $type;
        $this->primary = $primary;
        $this->company = $company;
        $this->street1 = $street1;
        $this->street2 = $street2;
        $this->city = $city;
        $this->state = $state;
        $this->zip = $zip;
        $this->phone = $phone;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return boolean
     */
    public function isPrimary()
    {
        return (bool) $this->primary;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getStreet1()
    {
        return $this->street1;
    }

    /**
     * @return string
     */
    public function getStreet2()
    {
        return $this->street2;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
}
