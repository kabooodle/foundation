<?php

namespace Kabooodle\Bus\Events\Flashsale;

use Kabooodle\Models\FlashSales;

/**
 * Class FlashsaleWasUpdatedEvent
 * @package Kabooodle\Bus\Events\Flashsale
 */
class FlashsaleWasUpdatedEvent
{
    /**
     * FlashsaleWasUpdatedEvent constructor.
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