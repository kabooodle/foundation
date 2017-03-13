<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Email;

use Kabooodle\Models\Email;
use Kabooodle\Bus\Events\Event;

/**
 * Class EmailWasCreatedEvent
 * @package Kabooodle\Bus\Events\User
 */
class EmailWasCreatedEvent extends Event
{
    /**
     * @var Email
     */
    private $email;

    /**
     * EmailWasCreatedEvent constructor.
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
    public function getEmail()
    {
        return $this->email;
    }
}
