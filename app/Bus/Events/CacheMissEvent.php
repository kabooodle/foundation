<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events;

/**
 * Class CacheMissEvent
 * @package Kabooodle\Bus\Events
 */
final class CacheMissEvent extends Event
{
    /**
     * @var string|integer
     */
    private $tagName;

    /**
     * @var string|integer
     */
    private $key;

    /**
     * CacheMissEvent constructor.
     *
     * @param $tagName
     * @param $key
     */
    public function __construct($tagName, $key)
    {
        $this->tagName = $tagName;
        $this->key = $key;
    }

    /**
     * @return int|string
     */
    public function getTagName()
    {
        return $this->tagName;
    }

    /**
     * @return int|string
     */
    public function getKey()
    {
        return $this->key;
    }
}
