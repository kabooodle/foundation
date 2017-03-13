<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\User;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Events\User\UserWasCreatedEvent;
use Kabooodle\Bus\Commands\Subscriptions\SubscribeUserToGenericTrialCommand;

/**
 * Class AddNewUserToGenericTrial
 */
class AddNewUserToGenericTrial
{
    use DispatchesJobs;

    /**
     * @param UserWasCreatedEvent $event
     */
    public function handle(UserWasCreatedEvent $event)
    {
        $user = $event->getUser();
        if ($event->getAccountType() == 'merchant') {
            $this->dispatchNow(new SubscribeUserToGenericTrialCommand($user));
        }
    }
}
