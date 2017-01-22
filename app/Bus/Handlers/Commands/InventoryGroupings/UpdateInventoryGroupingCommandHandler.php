<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\InventoryGroupings;

use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\InventoryGroupings\UpdateInventoryGroupingCommand;

/**
 * Class UpdateInventoryGroupingCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\InventoryGroupings
 */
class UpdateInventoryGroupingCommandHandler
{
    use DispatchesJobs;

    /**
     * @param UpdateInventoryGroupingCommand $command
     *
     * @return array
     */
    public function handle(UpdateInventoryGroupingCommand $command)
    {

    }
}
