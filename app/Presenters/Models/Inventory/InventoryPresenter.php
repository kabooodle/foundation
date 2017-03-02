<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Presenters\Models\Inventory;

use Kabooodle\Models\Inventory;
use Kabooodle\Presenters\PresenterAbstract;

/**
 * Class InventoryPresenter
 * @package Kabooodle\Presenters\Models\Inventory
 */
class InventoryPresenter extends PresenterAbstract
{
    public function listableShowOutfitSection()
    {
        return Inventory::class;
    }
}
