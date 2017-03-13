<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
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
    protected $objectId;

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
    protected $name;

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
     * @var string
     */
    protected $metadata;

    /**
     * AddAddressCommand constructor.
     * @param User $user
     * @param $objectId
     * @param $type
     * @param $primary
     * @param $name
     * @param $company
     * @param $street1
     * @param $street2
     * @param $city
     * @param $state
     * @param $zip
     * @param $phone
     * @param $metadata
     */
    public function __construct(
        User $user,
        $objectId,
        $type,
        $primary,
        $name,
        $company,
        $street1,
        $street2,
        $city,
        $state,
        $zip,
        $phone,
        $metadata)
    {
        $this->user = $user;
        $this->objectId = $objectId;
        $this->type = $type;
        $this->primary = $primary;
        $this->name = $name;
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
    public function getObjectId()
    {
        return $this->objectId;
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
    public function getName()
    {
        return $this->name;
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

    /**
     * @return string
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
}
