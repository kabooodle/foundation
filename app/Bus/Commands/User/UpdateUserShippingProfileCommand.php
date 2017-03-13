<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\User;

use Kabooodle\Models\User;

/**
 * Class UpdateUserShippingProfileCommand
 */
final class UpdateUserShippingProfileCommand
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var bool;
     */
    public $kabooodleAsDefaultShippingProvider;

    /**
     * UpdateUserShippingProfileCommand constructor.
     * @param User $user
     * @param bool $kabooodleAsDefaultShippingProvider
     */
    public function __construct(User $user, bool $kabooodleAsDefaultShippingProvider = true)
    {
        $this->user = $user;
        $this->kabooodleAsDefaultShippingProvider = $kabooodleAsDefaultShippingProvider;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return bool
     */
    public function isKabooodleDefaultShippingProvider(): bool
    {
        return (bool) $this->kabooodleAsDefaultShippingProvider;
    }
}
