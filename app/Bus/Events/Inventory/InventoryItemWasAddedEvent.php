<?php

namespace Kabooodle\Bus\Events\Inventory;

use Kabooodle\Models\Inventory;

/**
 * Class InventoryItemWasAddedEvent
 * @package Kabooodle\Bus\Events\Inventory
 */
final class InventoryItemWasAddedEvent
{
    /**
     * InventoryItemWasAddedEvent constructor.
     *
     * @param Inventory $item
     */
    public function __construct(Inventory $item)
    {
        $this->item = $item;
    }

    /**
     * @return Inventory
     */
    public function getItem()
    {
        return $this->item;
    }
}