<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Presenters\Models\InventoryGrouping;

use Kabooodle\Models\InventoryGrouping;
use Kabooodle\Presenters\PresenterAbstract;

/**
 * Class InventoryGroupingPresenter
 * @package Kabooodle\Presenters\Models\InventoryGrouping
 */
class InventoryGroupingPresenter extends PresenterAbstract
{
    public function listableShowOutfitSection()
    {
        return InventoryGrouping::class;
    }
}
