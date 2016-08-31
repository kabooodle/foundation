<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Events;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Class BroadcastEvent
 * @package Kabooodle\Bus\Events
 */
abstract class BroadcastEvent implements ShouldBroadcast
{
    public $unreadNotificationsCount = 0;

    /**
     * BroadcastEvent constructor.
     */
    public function __construct()
    {
        $this->unreadNotificationsCount = rand(0,100);
    }
}