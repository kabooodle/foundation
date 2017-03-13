<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events;

/**
 * Class BroadcastEventListener
 * @package Kabooodle\Bus\Handlers\Events
 */
class BroadcastEventListener
{
    public function handle($event)
    {
        //
        \Log::info('hi');
    }

    public function subscribe($events)
    {
        $events->listen(
            '*Broadcaster',
            'Kabooodle\Bus\Handlers\Events\BroadcastEventListener@handle'
        );
    }
}
