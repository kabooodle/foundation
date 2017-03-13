<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\User;

use Kabooodle\Models\User;
use Kabooodle\Bus\Events\Event;

/**
 * Class UserWasCreatedEvent
 * @package Kabooodle\Bus\Events\User
 */
final class UserWasCreatedEvent extends Event
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var string
     */
    public $accountType;

    /**
     * @param User   $user
     * @param string $accountType
     */
    public function __construct(User $user, string $accountType)
    {
        $this->user = $user;
        $this->accountType = $accountType;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getAccountType(): string
    {
        return $this->accountType;
    }
}
