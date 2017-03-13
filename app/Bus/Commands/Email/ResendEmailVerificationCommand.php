<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Email;

use Kabooodle\Models\Email;

/**
 * Class ResendEmailVerificationCommand
 * @package Kabooodle\Bus\Commands\User
 */
final class ResendEmailVerificationCommand
{
    /**
     * @var Email
     */
    public $email;

    /**
     * ResendEmailVerificationCommand constructor.
     *
     * @param Email $email
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }
}
