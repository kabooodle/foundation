<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\InventoryGroupings;

use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\InventoryGroupings\DestroyInventoryGroupingCommand;

/**
 * Class DestroyInventoryGroupingCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\InventoryGroupings
 */
class DestroyInventoryGroupingCommandHandler
{
    use DispatchesJobs;

    /**
     * @param DestroyInventoryGroupingCommand $command
     *
     * @return array
     */
    public function handle(DestroyInventoryGroupingCommand $command)
    {

    }
}
