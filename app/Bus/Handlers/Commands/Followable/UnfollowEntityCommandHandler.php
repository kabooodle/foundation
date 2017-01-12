<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Followable;

use DB;
use Kabooodle\Bus\Commands\Followable\UnfollowEntityCommand;
use Kabooodle\Models\Follows;

/**
 * Class UnfollowEntityCommandHandler
 */
class UnfollowEntityCommandHandler
{
    /**
     * @param UnfollowEntityCommandHandler $command
     *
     * @return mixed
     */
    public function handle(UnfollowEntityCommand $command)
    {
        $actor = $command->getActor();

        /** @var $followable */
        $followable = $command->getFollowable();

        return DB::transaction(function() use ($actor, $followable) {
            $follow = Follows::where('user_id', $actor->id)
                ->where('watchable_id', $followable->id)
                ->where('watchable_type', get_class($followable))
                ->first();

            return $follow ? $follow->delete() : false;
        });
    }
}