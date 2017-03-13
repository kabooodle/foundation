<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Inventory;

use Kabooodle\Models\User;

/**
 * Class InventoryItemWasAddedToSaleEvent
 * @package Kabooodle\Bus\Events\Inventory
 */
final class InventoryItemWasAddedToSaleEvent
{
    /**
     * InventoryItemWasAddedToSaleEvent constructor.
     *
     * @param User $user
     * @param int $flashSaleId
     * @param int $inventoryId
     */
    public function __construct(User $user, $flashSaleId, $inventoryId)
    {
        $this->user = $user;
        $this->flashsaleId = $flashSaleId;
        $this->inventoryId = $inventoryId;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return int
     */
    public function getFlashsaleId()
    {
        return $this->flashsaleId;
    }

    /**
     * @return int
     */
    public function getInventoryId()
    {
        return $this->inventoryId;
    }
}
