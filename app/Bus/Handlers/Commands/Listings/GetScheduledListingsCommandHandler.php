<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use DB;
use Kabooodle\Models\Listings;
use Kabooodle\Bus\Commands\Listings\GetScheduledListingsCommand;

/**
 * Class GetScheduledListingsCommandHandler
 */
class GetScheduledListingsCommandHandler
{
    /**
     * @param GetScheduledListingsCommand $command
     * @return mixed
     */
    public function handle(GetScheduledListingsCommand $command)
    {
        return Listings::facebook()
            ->scheduledFor('>=', $command->getStartTime()->format('Y-m-d H:i:s'))
            ->scheduledFor('<=', $command->getEndTime()->format('Y-m-d H:i:s'))
            ->statusScheduled()
            ->randomize()
            ->get();
    }
}