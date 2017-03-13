<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Libraries\Linkify;

/**
 * Class LinkifyableTrait
 * @package Kabooodle\Libraries\Linkify
 */
trait LinkifyableTrait
{
    /**
     * @var Linkify
     */
    private static $linkinstance;

    /**
     * @param $text
     *
     * @return string
     */
    public function linkify($text)
    {
        $instance = $this->getInstance();

        return $instance->process($text);
    }

    /**
     * @return Linkify
     */
    protected function getInstance()
    {
        if (! self::$linkinstance) {
            self::$linkinstance = new Linkify;
        }

        return self::$linkinstance;
    }
}
