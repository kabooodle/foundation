<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Flashsale;

use Kabooodle\Models\FlashSales;

/**
 * Class FlashsaleWasCreatedEvent
 * @package Kabooodle\Bus\Events\Group
 */
class FlashsaleWasCreatedEvent
{
    /**
     * FlashsaleWasCreatedEvent constructor.
     *
     * @param FlashSales $flashSales
     */
    public function __construct(FlashSales $flashSales)
    {
        $this->flashSale = $flashSales;
    }

    /**
     * @return FlashSales
     */
    public function getFlashSale()
    {
        return $this->flashSale;
    }
}