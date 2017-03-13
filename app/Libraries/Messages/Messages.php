<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Libraries\Messages;

use Closure;
use Illuminate\Session\Store as SessionStore;
use Illuminate\Support\MessageBag as M;

/**
 * Class Messages
 * @package Kabooodle\Libraries\Messages
 */
class Messages extends M implements MessagesInterface
{
    /**
     * The session store instance.
     *
     * @var \Illuminate\Session\Store
     */
    protected $session;

    /**
     * Cached messages to be extends to current request.
     *
     * @var static
     */
    protected $instance;

    /**
     * Set the session store.
     *
     * @param  \Illuminate\Session\Store   $session
     *
     * @return $this
     */
    public function setSessionStore(SessionStore $session)
    {
        $this->session  = $session;
        $this->instance = null;

        return $this;
    }

    /**
     * Get the session store.
     *
     * @return \Illuminate\Session\Store
     */
    public function getSessionStore()
    {
        return $this->session;
    }

    /**
     * Extend Messages instance from session.
     *
     * @param  \Closure $callback
     *
     * @return static
     */
    public function extend(Closure $callback)
    {
        $instance = $this->retrieve();

        $callback($instance);

        return $instance;
    }

    /**
     * Retrieve Message instance from Session, the data should be in
     * serialize, so we need to unserialize it first.
     *
     * @return static
     */
    public function retrieve()
    {
        $messages = null;

        if (is_null($this->instance)) {
            $this->instance = new static();
            $this->instance->setSessionStore($this->session);

            if ($this->session->has('message')) {
                $messages = unserialize($this->session->pull('message'));
            }

            if (is_array($messages)) {
                $this->instance->merge($messages);
            }
        }

        return $this->instance;
    }

    /**
     * Store current instance.
     *
     * @return void
     */
    public function save()
    {
        $this->session->flash('message', $this->serialize());
        $this->instance = null;
    }

    /**
     * Compile the instance into serialize.
     *
     * @return string   serialize of this instance
     */
    public function serialize()
    {
        return serialize($this->messages);
    }

    /**
     * @param string $msg
     */
    public function success($msg)
    {
        $this->add('success', $msg);
    }

    /**
     * @param string $msg
     */
    public function error($msg)
    {
        $this->add('error', $msg);
    }
}
