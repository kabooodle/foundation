<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

/**
 * Class BroadcastableTrait
 */
trait BroadcastableTrait
{
    /**
     * @var PusherManager
     */
    static $_pusherManager;

    /**
     * @return PusherManager
     */
    public function getPusher()
    {
        if (! self::$_pusherManager) {
            self::$_pusherManager = app('pusher');
        }

        return self::$_pusherManager;
    }
}
