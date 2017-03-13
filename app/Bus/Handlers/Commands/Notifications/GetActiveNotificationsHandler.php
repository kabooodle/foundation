<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Notifications;

use Kabooodle\Models\Notifications;
use Illuminate\Cache\Repository as CacheRepository;
use Kabooodle\Bus\Commands\Notifications\GetActiveNotifications;

/**
 * Class GetActiveNotificationsHandler
 * @package Kabooodle\Bus\Handlers\Commands\Notifications
 */
class GetActiveNotificationsHandler
{
    const TAG = 'kaboodle_notifications';

    /**
     * GetActiveNotificationsHandler constructor.
     *
     * @param CacheRepository $cache
     */
    public function __construct(CacheRepository $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param GetActiveNotifications $command
     *
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function handle(GetActiveNotifications $command)
    {
        if ($this->cache->has(self::TAG)) {
            return $this->cache->get(self::TAG);
        }

        $notifications = Notifications::where('active', 1)->get();
        $this->cache->add(self::TAG, $notifications, 43800);

        return $notifications;
    }
}
