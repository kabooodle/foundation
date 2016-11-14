<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\User;

use Kabooodle\Bus\Events\User\UserSubscriptionCameOffTrial;

/**
 * Class UserSubscriptionCameOffTrialListener
 * @package Kabooodle\Bus\Handlers\Events\User
 */
class UserSubscriptionCameOffTrialListener
{
    public function handle(UserSubscriptionCameOffTrial $event)
    {
    }
}
