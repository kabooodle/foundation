<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Kabooodle\Bus\Commands\Listings\ScheduleNewListingsCommand;

/**
 * Class ScheduleNewListingsCommandHandler
 */
class ScheduleNewListingsCommandHandler
{
    use DispatchesJobs;

    /**
     * @param ScheduleNewListingsCommand $command
     */
    public function handle(ScheduleNewListingsCommand $command)
    {
        DB::transaction(function() use ($command) {
            if ($facebookJob = $command->getFacebookListings()) {
                $this->dispatchNow($facebookJob);
            }

            if ($flashsaleJob = $command->getFlashsaleListings()) {
                $this->dispatchNow($flashsaleJob);
            }
        });
    }
}
