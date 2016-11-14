<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
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
    public $name;

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
    public $referralId;

    /**
     * AddUserCommand constructor.
     *
     * @param string    $name
     * @param string    $email
     * @param string    $password
     */
    public function __construct($name, $email, $password, $referralId = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->referralId = $referralId;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getReferralId()
    {
        return $this->referralId;
    }
}
