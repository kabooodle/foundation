<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\User;

use Kabooodle\Models\User;

/**
 * Class ExtendFacebookAccessToken
 */
final class ExtendFacebookAccessTokenCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var string
     */
    public $token;

    /**
     * @param User $actor
     */
    public function __construct(User $actor, string $token)
    {
        $this->actor = $actor;
        $this->token = $token;
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
    public function getToken(): string
    {
        return $this->token;
    }
}
