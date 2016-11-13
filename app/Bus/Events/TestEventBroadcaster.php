<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Class TestEvent
 */
class TestEventBroadcaster extends BroadcastEvent implements ShouldBroadcast
{
    use SerializesModels;

    public $text;
    public $user;

    public function __construct($text)
    {
        parent::__construct();
        $this->text = $text;
        $this->user = user();
    }

    public function broadcastOn()
    {
        return ['private_'.$this->user->username];
    }

    /**
     * @return string
     */
    public function broadcastAs()
    {
        return 'kabooodle.testevent';
    }
}
