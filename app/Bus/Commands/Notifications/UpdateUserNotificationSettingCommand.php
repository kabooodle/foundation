<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Notifications;

use Kabooodle\Models\User;

/**
 * Class UpdateUserNotificationSettingCommand
 * @package Kabooodle\Bus\Commands\Notifications
 */
final class UpdateUserNotificationSettingCommand
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var int
     */
    public $notificationId;

    /**
     * @var string
     */
    public $notificationType;

    /**
     * @var string
     */
    public $notificationAction;

    /**
     * UpdateUserNotificationSettingCommand constructor.
     *
     * @param User   $user
     * @param int    $notificationId
     * @param string $notificationType
     * @param string $notificationAction
     */
    public function __construct(User $user, int $notificationId, string $notificationType = 'email', string $notificationAction = 'subscribed')
    {
        $this->user = $user;
        $this->notificationId = $notificationId;
        $this->notificationType = $notificationType;
        $this->notificationAction = $notificationAction;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getNotificationId(): int
    {
        return $this->notificationId;
    }

    /**
     * @return string
     */
    public function getNotificationType(): string
    {
        return $this->notificationType;
    }

    /**
     * @return string
     */
    public function getNotificationAction(): string
    {
        return $this->notificationAction;
    }
}