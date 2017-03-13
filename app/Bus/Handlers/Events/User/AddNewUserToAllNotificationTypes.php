<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\User;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Events\User\UserWasCreatedEvent;
use Kabooodle\Bus\Commands\Notifications\GetActiveNotifications;

/**
 * Class AddNewUserToAllNotificationTypes
 */
class AddNewUserToAllNotificationTypes
{
    use DispatchesJobs;

    /**
     * @param UserWasCreatedEvent $event
     */
    public function handle(UserWasCreatedEvent $event)
    {
        $user = $event->getUser();
        $notifications = $this->dispatchNow(new GetActiveNotifications);
        $user->notificationsettings()->saveMany($notifications);
    }
}