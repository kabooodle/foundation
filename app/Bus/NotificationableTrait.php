<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
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
        $setting = $this->notificationsettings->where('name', $name)->first();
        if ($setting) {
            return $setting->{$type} == true;
        }

        return false;
    }
}
