<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Kabooodle\Models\Watches;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Class WatchableTrait
 */
trait WatchableTrait
{
    /**
     * @return mixed
     */
    public function watchers():MorphMany
    {
        return $this->morphMany(Watches::class, 'watchable');
    }

    /**
     * @return bool
     */
    public function getIsWatchedAttribute()
    {
        if (user()) {
            $follow = $this->watchers->filter(function ($follow) {
                return $follow->user_id = user()->id;
            })->first();

            return $follow ? true : false;
        }
        return false;
    }
}
