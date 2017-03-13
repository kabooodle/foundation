<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Email;

use Kabooodle\Models\Email;

/**
 * Class MakeEmailPrimaryCommand
 * @package Kabooodle\Bus\Commands\User
 */
final class MakeEmailPrimaryCommand
{
    /**
     * @var Email
     */
    public $email;

    /**
     * MakeEmailPrimaryCommand constructor.
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
