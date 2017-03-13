<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Emojione\Client;
use Kabooodle\Libraries\Emojify\Emojify;

/**
 * Class EmojifyableTrait
 * @package Kabooodle\Models\Traits
 */
trait EmojifyableTrait
{
    /**
     * @var Client
     */
    public static $emojiInstance;

    public static function bootEmojifyableTrait()
    {
        if (! self::$emojiInstance) {
            self::$emojiInstance = Emojify::getInstance();
        }
    }

    /**
     * @param $string
     *
     * @return string
     */
    public function emojify($string)
    {
        return self::$emojiInstance->toImage($string);
    }
}
