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
final class AddUserCommand
{
    /**
     * @var string
     */
    public $firstName;

    /**
     * @var string
     */
    public $lastName;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $accountType;

    /**
     * @var string
     */
    public $timezone;

    /**
     * @var string|null
     */
    public $referralUsername;

    /**
     * @param             $firstName
     * @param             $lastName
     * @param             $username
     * @param             $email
     * @param             $password
     * @param string      $accountType
     * @param string      $timezone
     * @param string|null $referralUsername
     */
    public function __construct($firstName, $lastName, $username, $email, $password, string $accountType, string $timezone, string $referralUsername = null)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->accountType = $accountType;
        $this->timezone = $timezone;
        $this->referralUsername = $referralUsername;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getAccountType(): string
    {
        return $this->accountType;
    }

    /**
     * @return string
     */
    public function getTimezone(): string
    {
        return $this->timezone;
    }

    /**
     * @return mixed
     */
    public function getReferralUsername()
    {
        return $this->referralUsername;
    }
}
