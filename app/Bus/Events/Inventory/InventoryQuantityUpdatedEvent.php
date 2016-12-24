<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Inventory;

use Kabooodle\Models\Inventory;

/**
 * Class InventoryQuantityUpdatedEvent
 */
final class InventoryQuantityUpdatedEvent
{
    /**
     * @var Inventory
     */
    public $inventoryItem;

    /**
     * @param Inventory $inventoryItem
     */
    public function __construct(Inventory $inventoryItem)
    {
        $this->inventoryItem = $inventoryItem;
    }

    /**
     * @return Inventory
     */
    public function getInventoryItem(): Inventory
    {
        return $this->inventoryItem;
    }
}
