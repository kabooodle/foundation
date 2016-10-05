<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Libraries\Emojify;

use Emojione\Client;
use Emojione\Ruleset;

/**
 * Class Emojify
 * @package Kabooodle\Libraries\Emojify
 */
class Emojify
{
    /**
     * @var Client
     */
    public static $client;

    /**
     * @return Client
     */
    public static function getInstance()
    {
        if (!self::$client) {
            self::$client = new Client(new Ruleset());
        }

        self::$client->ascii = true;
        self::$client->sprites = true;
        return self::$client;
    }
}