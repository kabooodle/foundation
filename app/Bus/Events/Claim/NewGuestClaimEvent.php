<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Claim;

use Kabooodle\Models\Claims;
use Kabooodle\Models\Email;

/**
 * Class NewGuestClaimEvent
 * @package Kabooodle\Bus\Events\Claim
 */
final class NewGuestClaimEvent
{
    /**
     * @var Claims
     */
    private $claim;

    /**
     * @var Email
     */
    private $email;

    /**
     * NewGuestClaimEvent constructor.
     * @param Claims $claim
     * @param Email $email
     */
    public function __construct(Claims $claim, Email $email)
    {
        $this->claim = $claim;
        $this->email = $email;
    }

    /**
     * @return Claims
     */
    public function getClaim(): Claims
    {
        return $this->claim;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }
}
