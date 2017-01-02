<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Vinkla\Pusher\PusherManager;

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
            self::$_pusherManager = app(PusherManager::class);
        }

        return self::$_pusherManager;
    }
}
