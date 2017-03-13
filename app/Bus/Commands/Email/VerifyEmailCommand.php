<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Email;

/**
 * Class VerifyEmailCommand
 * @package Kabooodle\Bus\Commands\User
 */
final class VerifyEmailCommand
{
    /**
     * @var string
     */
    public $token;

    /**
     * VerifyEmailCommand constructor.
     *
     * @param $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }
}
