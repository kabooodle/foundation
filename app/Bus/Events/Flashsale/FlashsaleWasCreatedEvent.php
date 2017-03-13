<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Flashsale;

/**
 * Class FlashsaleWasCreatedEvent
 * @package Kabooodle\Bus\Events\Group
 */
final class FlashsaleWasCreatedEvent
{
    /**
     * @var int
     */
    public $flashsaleId;

    /**
     * @param int $flashsaleId
     */
    public function __construct(int $flashsaleId)
    {
        $this->flashsaleId = $flashsaleId;
    }

    /**
     * @return int
     */
    public function getFlashsaleId(): int
    {
        return $this->flashsaleId;
    }
}
