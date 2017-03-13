<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Inventory;

use Kabooodle\Models\User;
use Kabooodle\Models\Inventory;
use Kabooodle\Models\FlashSales;

/**
 * Class InventoryItemWasRemovedFromSale
 * @package Kabooodle\Bus\Events\Inventory
 */
final class InventoryItemWasRemovedFromSaleEvent
{
    /**
     * InventoryItemWasRemovedFromSale constructor.
     *
     * @param User       $user
     * @param Inventory  $item
     * @param FlashSales $flashsale
     */
    public function __construct(User $user, Inventory $item, FlashSales $flashsale)
    {
        $this->user = $user;
        $this->item = $item;
        $this->flashsale = $flashsale;
    }

    /**
     * @return FlashSales
     */
    public function getFlashsale()
    {
        return $this->flashsale;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return Inventory
     */
    public function getInventoryItem()
    {
        return $this->item;
    }
}
