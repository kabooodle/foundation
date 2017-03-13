<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
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
            self::$client = new EmojifyClient(new Ruleset());
        }

        self::$client->htmlClasses = ' emojione-ascii emojione-no-sprites ';
        self::$client->ascii = true;
        self::$client->sprites = false;
        return self::$client;
    }
}
