<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\User;
use Kabooodle\Models\Email;
use Kabooodle\Models\User;

/**
 * Class ConvertGuestToUserCommand
 * @package Kabooodle\Bus\Commands\User
 */
final class ConvertGuestToUserCommand
{
    /**
     * @var User
     */
    protected $guest;

    /**
     * @var Email
     */
    protected $email;

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
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string|null
     */
    protected $referralUsername;

    /**
     * @param User        $guest
     * @param Email       $email
     * @param             $firstName
     * @param             $lastName
     * @param             $username
     * @param             $password
     * @param string|null $referralUsername
     */
    public function __construct(User $guest, Email $email, $firstName, $lastName, $username, $password, string $referralUsername = null)
    {
        $this->guest = $guest;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->username = $username;
        $this->password = $password;
        $this->referralUsername = $referralUsername;
    }

    /**
     * @return User
     */
    public function getGuest(): User
    {
        return $this->guest;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
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
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return mixed
     */
    public function getReferralUsername()
    {
        return $this->referralUsername;
    }
}
