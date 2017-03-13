<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Watchable;

use DB;
use Kabooodle\Models\Watches;
use Kabooodle\Models\Contracts\WatchableInterface;
use Kabooodle\Bus\Commands\Watchable\UnwatchEntityCommand;

/**
 * Class UnwatchEntityCommandHandler
 */
class UnwatchEntityCommandHandler
{
    /**
     * @param UnwatchEntityCommand $command
     *
     * @return mixed
     */
    public function handle(UnwatchEntityCommand $command)
    {
        $actor = $command->getActor();

        /** @var WatchableInterface $watchable */
        $watchable = $command->getWatchable();

        return DB::transaction(function() use ($actor, $watchable) {
            $watch = Watches::where('user_id', $actor->id)
                ->where('watchable_id', $watchable->id)
                ->where('watchable_type', get_class($watchable))
                ->first();

            return $watch ? $watch->delete() : false;
        });
    }
}
