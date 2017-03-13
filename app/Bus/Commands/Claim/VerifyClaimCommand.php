<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Claim;

/**
 * Class VerifyClaimCommand
 * @package Kabooodle\Bus\Commands\User
 */
final class VerifyClaimCommand
{
    /**
     * @var string
     */
    public $token;

    /**
     * VerifyClaimCommand constructor.
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
