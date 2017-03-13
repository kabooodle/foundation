<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\PhoneNumbers;

use Kabooodle\Models\User;
use Kabooodle\Models\PhoneNumber;

/**
 * Class PhoneNumberWasVerifiedSuccessfullyEvent
 */
final class PhoneNumberWasVerifiedSuccessfullyEvent
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
    public function getPhoneNumberModel(): PhoneNumber
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
