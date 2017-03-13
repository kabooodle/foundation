<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\PhoneNumbers;

use Kabooodle\Models\User;

/**
 * Class StartNewVerificationCommand
 */
final class StartNewVerificationCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var string
     */
    public $phoneNumber;

    /**
     * @param User   $actor
     * @param string $phoneNumber
     */
    public function __construct(User $actor, string $phoneNumber)
    {
        $this->actor = $actor;
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }
}
