<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Watchable;

use DB;
use Kabooodle\Models\Watches;
use Kabooodle\Models\Contracts\WatchableInterface;
use Kabooodle\Bus\Events\Watchable\UserWatchedEntityEvent;
use Kabooodle\Bus\Commands\Watchable\WatchNewEntityCommand;

/**
 * Class WatchNewEntityCommandHandler
 */
class WatchNewEntityCommandHandler
{
    /**
     * @param WatchNewEntityCommand $command
     *
     * @return bool
     */
    public function handle(WatchNewEntityCommand $command)
    {
        $actor = $command->getActor();

        /** @var WatchableInterface $watchable */
        $watchable = $command->getWatchable();

        $alreadyFollowing = $watchable->watchers->filter(function ($follow) use ($actor) {
            return $follow->user_id = $actor->id;
        })->first();

        if ($alreadyFollowing) {
            return true;
        }

        return DB::transaction(function() use ($actor, $watchable) {
            $watch = Watches::create([
                'user_id' => $actor->id,
                'watchable_id' => $watchable->id,
                'watchable_type' => get_class($watchable)
            ]);

            event(new UserWatchedEntityEvent($actor, $watch, $watchable));
        });
    }
}
