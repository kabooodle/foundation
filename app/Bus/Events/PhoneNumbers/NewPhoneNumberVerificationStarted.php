<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\PhoneNumbers;

use Kabooodle\Models\User;
use Kabooodle\Models\PhoneNumber;

/**
 * Class NewPhoneNumberVerificationStarted
 */
final class NewPhoneNumberVerificationStarted
{
    /**
     * @var PhoneNumber
     */
    public $phoneNumber;

    /**
     * @var User
     */
    public $actor;

    /**
     * @param PhoneNumber $phoneNumber
     * @param User        $actor
     */
    public function __construct(PhoneNumber $phoneNumber, User $actor)
    {
        $this->phoneNumber = $phoneNumber;
        $this->actor = $actor;
    }

    /**
     * @return PhoneNumber
     */
    public function getPhoneNumber(): PhoneNumber
    {
        return $this->phoneNumber;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }
}
