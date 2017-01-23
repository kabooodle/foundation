<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
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
    public $flashsaldId;

    /**
     * @param int $flashsaleId
     */
    public function __construct(int $flashsaleId)
    {
        $this->flashSaleId = $flashsaleId;
    }

    /**
     * @return int
     */
    public function getFlashSaleId(): int
    {
        return $this->flashsaleId;
    }
}
