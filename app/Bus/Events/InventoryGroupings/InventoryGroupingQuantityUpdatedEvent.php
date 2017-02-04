<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\InventoryGroupings;

use Kabooodle\Models\InventoryGrouping;

/**
 * Class InventoryGroupingQuantityUpdatedEvent
 */
final class InventoryGroupingQuantityUpdatedEvent
{
    /**
     * @var InventoryGrouping
     */
    public $grouping;

    /**
     * @param InventoryGrouping $grouping
     */
    public function __construct(InventoryGrouping $grouping)
    {
        $this->grouping = $grouping;
    }

    /**
     * @return InventoryGrouping
     */
    public function getGrouping(): InventoryGrouping
    {
        return $this->grouping;
    }
}
