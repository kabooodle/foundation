<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Libraries\Messages;

use Illuminate\Support\Facades\Facade;

/**
 * Class MessagesFacade
 * @package Kabooodle\Libraries\Messages
 */
class MessagesFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'messages';
    }
}
