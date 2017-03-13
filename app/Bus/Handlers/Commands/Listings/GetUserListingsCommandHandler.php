<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Listings;

use Kabooodle\Models\Listings;
use Kabooodle\Bus\Commands\Listings\GetUserListingsCommand;

/**
 * Class GetUserListingsCommandHandler
 */
class GetUserListingsCommandHandler
{
    /**
     * @param GetUserListingsCommand $command
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function handle(GetUserListingsCommand $command)
    {
        return Listings::where('owner_id', $command->getActor()->id)->get();
    }
}
