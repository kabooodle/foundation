<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\User;

use Kabooodle\Bus\Events\User\UserLoggedInEvent;

/**
 * Class UserLoggedInListener
 * @package Kabooodle\Bus\Handlers\Events\User
 */
class UserLoggedInListener
{
    /**
     * @param UserLoggedInEvent $event
     */
    public function handle(UserLoggedInEvent $event)
    {
    }
}
