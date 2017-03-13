<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Libraries\WebSockets;

use Kabooodle\Models\Traits\BroadcastableTrait;

/**
 * Class AbstractWebSocket
 */
class WebSocket
{
    use BroadcastableTrait;

    /**
     * @var null|string
     */
    public $channelName;

    /**
     * @var null|string
     */
    public $eventName;

    /**
     * @var array
     */
    public $payload;

    /**
     * @param null|string  $channelName
     * @param null|string  $eventName
     * @param array        $payload
     */
    public function __construct($channelName = null, $eventName = null, array $payload = [])
    {
        $this->channelName = $channelName;
        $this->eventName = $eventName;
        $this->payload = $payload;
    }

    /**
     * @return null
     */
    public function getChannelName()
    {
        return $this->channelName;
    }

    /**
     * @return null
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }

    /**
     * @param $eventName
     *
     * @return $this
     */
    public function setEventName($eventName)
    {
        $this->eventName = $eventName;

        return $this;
    }

    /**
     * @param array $payload
     *
     * @return $this
     */
    public function setPayload(array $payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @param $channelName
     *
     * @return $this
     */
    public function setChannelName($channelName)
    {
        $this->channelName = $channelName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function send()
    {
        $response = $this->getPusher()->trigger($this->getChannelName(), $this->getEventName(), $this->getPayload(), null, true);
    }
}
