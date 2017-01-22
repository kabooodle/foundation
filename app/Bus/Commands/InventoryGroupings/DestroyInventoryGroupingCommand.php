<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\InventoryGroupings;

use Kabooodle\Models\InventoryGrouping;

/**
 * Class DestroyInventoryGroupingCommand
 * @package Kabooodle\Bus\Commands\InventoryGroupings
 */
final class DestroyInventoryGroupingCommand
{
    /**
     * @var InventoryGrouping
     */
    public $inventoryGrouping;

    /**
     * DestroyInventoryGroupingCommand constructor.
     *
     * @param InventoryGrouping $inventoryGrouping
     */
    public function __construct(InventoryGrouping $inventoryGrouping)
    {
        $this->inventoryGrouping = $inventoryGrouping;
    }

    /**
     * @return InventoryGrouping
     */
    public function getInventoryGrouping(): InventoryGrouping
    {
        return $this->inventoryGrouping;
    }
}