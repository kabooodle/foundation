<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
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
     * @var bool
     */
    public $ignoreExpiredHolds;

    /**
     * @param      $token
     * @param bool $ignoreExpiredHolds
     */
    public function __construct($token, bool $ignoreExpiredHolds = false)
    {
        $this->token = $token;
        $this->ignoreExpiredHolds = $ignoreExpiredHolds;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return bool
     */
    public function shouldIgnoreExpireHolds(): bool
    {
        return $this->ignoreExpiredHolds;
    }
}
