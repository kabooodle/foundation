<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Libraries\Messages;

use Closure;
use Illuminate\Session\Store;

/**
 * Interface MessagesInterface
 * @package Kabooodle\Libraries\Messages
 */
interface MessagesInterface
{
    public function setSessionStore(Store $session);

    public function getSessionStore();

    public function add($key, $message);

    public function retrieve();

    public function extend(Closure $callback);

    public function save();

    public function serialize();

    public function success($msg);

    public function error($msg);
}
