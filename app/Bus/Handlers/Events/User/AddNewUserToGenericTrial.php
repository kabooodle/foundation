<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\User;

use Carbon\Carbon;
use Kabooodle\Bus\Events\User\UserWasCreatedEvent;

/**
 * Class AddNewUserToGenericTrial
 */
class AddNewUserToGenericTrial
{
    /**
     * @param UserWasCreatedEvent $event
     */
    public function handle(UserWasCreatedEvent $event)
    {
        $user = $event->getUser();
        if ($event->getAccountType() == 'merchant') {
            $user->trial_ends_at = Carbon::now()->addDays(30);
            $user->save();
        }
    }
}
