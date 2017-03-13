<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Libraries\LLRClient;

use Kabooodle\Models\LLRUser;
use GuzzleHttp\Cookie\SetCookie;

/**
 * Class LLRCredentials
 * @package Kabooodle\Libraries\LLRClients
 */
class LLRCredentials
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $rawPassword;

    /**
     * @var SetCookie
     */
    private $cookie;

    /**
     * LLRCredentials constructor.
     *
     * @param LLRUser $user
     */
    public function __construct(LLRUser $user)
    {
        $this->email = $user->email;
        $this->rawPassword = $user->decrypted_password;
        $this->cookie = $user->cookie;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->rawPassword;
    }

    /**
     * @return SetCookie
     */
    public function getCookie()
    {
        return $this->cookie;
    }
}
