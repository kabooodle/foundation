<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\User;

/**
 * Class AddUserCommand
 * @package Kabooodle\Bus\Commands\User
 */
final class AddGuestCommand
{
    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string|null
     */
    protected $company;

    /**
     * @var string
     */
    protected $street1;

    /**
     * @var string|null
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
     * @var string|null
     */
    protected $phone;

    /**
     * AddGuestCommand constructor.
     * @param $firstName
     * @param $lastName
     * @param $email
     * @param $company
     * @param $street1
     * @param $street2
     * @param $city
     * @param $state
     * @param $zip
     * @param $phone
     */
    public function __construct(
        $firstName,
        $lastName,
        $email,
        $company,
        $street1,
        $street2,
        $city,
        $state,
        $zip,
        $phone)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->company = $company;
        $this->street1 = $street1;
        $this->street2 = $street2;
        $this->city = $city;
        $this->state = $state;
        $this->zip = $zip;
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getStreet1(): string
    {
        return $this->street1;
    }

    /**
     * @return string|null
     */
    public function getStreet2()
    {
        return $this->street2;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getZip(): string
    {
        return $this->zip;
    }

    /**
     * @return string|null
     */
    public function getPhone()
    {
        return $this->phone;
    }
}
