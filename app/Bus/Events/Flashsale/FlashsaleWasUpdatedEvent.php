<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Flashsale;

/**
 * Class FlashsaleWasUpdatedEvent
 */
final class FlashsaleWasUpdatedEvent
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
    public function getFlashSaleId(): int
    {
        return $this->flashsaleId;
    }
}
