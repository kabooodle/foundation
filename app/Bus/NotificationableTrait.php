<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus;

/**
 * Class NotificationableEvent
 * @package Bus
 */
trait NotificationableTrait
{
    /**
     * @param string $name
     * @param string $type
     *
     * @return bool
     */
    public function checkIsNotifyable(string $name, string $type)
    {
        return $this->notificationsettings->filter(function($setting) use ($name, $type) {
            return $setting->name == $name && $setting->pivot->{$type} == true;
        })->first();
    }
}
