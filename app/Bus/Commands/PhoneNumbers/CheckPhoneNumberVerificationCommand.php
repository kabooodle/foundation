<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\PhoneNumbers;

use Kabooodle\Models\User;
use Kabooodle\Models\PhoneNumber;

/**
 * Class CheckPhoneNumberVerificationCommand
 */
final class CheckPhoneNumberVerificationCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var PhoneNumber
     */
    public $phoneNumber;

    /**
     * @var string
     */
    public $verificationCode;

    /**
     * @param User        $actor
     * @param PhoneNumber $phoneNumber
     * @param string      $verificationCode
     */
    public function __construct(User $actor, PhoneNumber $phoneNumber, string $verificationCode)
    {
        $this->actor = $actor;
        $this->phoneNumber = $phoneNumber;
        $this->verificationCode = $verificationCode;
    }

    /**
     * @return User
     */
    public function getActor(): User
    {
        return $this->actor;
    }

    /**
     * @return PhoneNumber
     */
    public function getPhoneNumberModel(): PhoneNumber
    {
        return $this->phoneNumber;
    }

    /**
     * @return string
     */
    public function getVerificationCode(): string
    {
        return $this->verificationCode;
    }
}
