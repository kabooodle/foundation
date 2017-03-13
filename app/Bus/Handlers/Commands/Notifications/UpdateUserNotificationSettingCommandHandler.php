<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Notifications;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Notifications\GetActiveNotifications;
use Kabooodle\Bus\Commands\Notifications\UpdateUserNotificationSettingCommand;

/**
 * Class UpdateUserNotificationSettingCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Notifications
 */
class UpdateUserNotificationSettingCommandHandler
{
    use DispatchesJobs;

    /**
     * @param UpdateUserNotificationSettingCommand $command
     *
     * @return bool
     */
    public function handle(UpdateUserNotificationSettingCommand $command)
    {
        $user = $command->getUser();
        $notificationId = $command->getNotificationId(); // notification id
        $action = $command->getNotificationAction(); // subscribed or unsubscribed
        $type = $command->getNotificationType(); // email or web
        $userNotificationSettings = $user->notificationsettings; // existing user notification settings

        $userNotificationSetting = $userNotificationSettings->find($notificationId);

        if ($userNotificationSetting) {
            $userNotificationSetting->pivot->{$type} = ($action == 'subscribed' ? 1 : ($action == 'unsubscribed' ? 0 : 1));
            $userNotificationSetting->pivot->save();
        } else {
            $user->notificationsettings()->attach($notificationId);
        }
    }
}
