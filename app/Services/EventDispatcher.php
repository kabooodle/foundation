<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Services;

use Kabooodle\Bus\Events\NotificationableEvent;
use Illuminate\Events\Dispatcher as BaseDispatcher;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
 * Class EventDispatcher
 * @package Kabooodle\Services
 */
class EventDispatcher extends BaseDispatcher
{
    /**
     * Fire an event and call the listeners.
     *
     * @param  string|object  $event
     * @param  mixed  $payload
     * @param  bool  $halt
     * @return array|null
     */
    public function fire($event, $payload = [], $halt = false)
    {
        // When the given "event" is actually an object we will assume it is an event
        // object and use the class as the event name and this event itself as the
        // payload to the handler, which makes object based events quite simple.
        if (is_object($event)) {
            list($payload, $event) = [[$event], get_class($event)];
        }

        $responses = [];

        // If an array is not given to us as the payload, we will turn it into one so
        // we can easily use call_user_func_array on the listeners, passing in the
        // payload to each of them so that they receive each of these arguments.
        if (! is_array($payload)) {
            $payload = [$payload];
        }

        $this->firing[] = $event;

        if (isset($payload[0]) && $payload[0] instanceof ShouldBroadcast) {
            $this->broadcastEvent($payload[0]);
        }

        foreach ($this->getListeners($event) as $listener) {
            $response = call_user_func_array($listener, $payload);

            // If a response is returned from the listener and event halting is enabled
            // we will just return this response, and not call the rest of the event
            // listeners. Otherwise we will add the response on the response list.
            if (! is_null($response) && $halt) {
                array_pop($this->firing);

                return $response;
            }

            // If a boolean false is returned from a listener, we will stop propagating
            // the event to any further listeners down in the chain, else we keep on
            // looping through the listeners and firing every one in our sequence.
            if ($response === false) {
                break;
            }

            $responses[] = $response;
        }

        array_pop($this->firing);

        return $halt ? null : $responses;
    }
}
