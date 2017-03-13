<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Events\Inventory;

use Kabooodle\Bus\Events\Inventory\InventoryItemWasAddedEvent;

/**
 * Class InventoryItemWasAddedEventHandler
 * @package Kabooodle\Bus\Handlers\Events\Inventory
 */
class InventoryItemWasAddedEventHandler
{
    /**
     * @param InventoryItemWasAddedEvent $event
     */
    public function handle(InventoryItemWasAddedEvent $event)
    {
    }
}
