<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Followable;

use DB;
use Kabooodle\Bus\Commands\Followable\FollowNewEntityCommand;
use Kabooodle\Models\Follows;
use Kabooodle\Models\Watches;
use Kabooodle\Models\Contracts\WatchableInterface;
use Kabooodle\Bus\Events\Watchable\UserWatchedEntityEvent;
use Kabooodle\Bus\Commands\Watchable\WatchNewEntityCommand;

/**
 * Class FollowNewEntityCommandHandler
 */
class FollowNewEntityCommandHandler
{
    /**
     * @param FollowNewEntityCommandHandler $command
     *
     * @return bool
     */
    public function handle(FollowNewEntityCommand $command)
    {
        $actor = $command->getActor();

        /** @var  */
        $followable = $command->getFollowable();

        if ($followable->is_followed) {
            return true;
        }

        return DB::transaction(function() use ($actor, $followable) {
            $follow = Follows::create([
                'user_id' => $actor->id,
                'followable_id' => $followable->id,
                'followable_type' => get_class($followable)
            ]);

            //event(new UserWatchedEntityEvent($actor, $watch, $watchable));
        });
    }
}