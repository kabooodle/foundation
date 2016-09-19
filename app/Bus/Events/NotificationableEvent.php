<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Events;

use Kabooodle\Models\Contracts\NotificationableInterface;

/**
 * Class NotificationableEvent
 * @package Kabooodle\Bus\Events
 */
class NotificationableEvent
{
    /**
     * @var NotificationableInterface
     */
    protected $event;

    /**
     * @var
     */
    protected $payload;

    /**
     * NotificationableEvent constructor.
     *
     * @param NotificationableInterface $event
     * @param                           $payload
     */
    public function __construct($event,  $payload)
    {
        $this->event = $event;
        $this->payload = $payload;
    }

    /**
     * @return NotificationableInterface
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }
}