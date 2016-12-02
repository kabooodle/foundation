<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Auth;
use Kabooodle\Models\Follows;

/**
 * Class FollowableTrait
 * @package Kabooodle\Models\Traits
 */
trait FollowableTrait
{
    /**
     * @return mixed
     */
    public function followers()
    {
        return $this->morphMany(Follows::class, 'followable')->where('deleted_at', null);
    }

    /**
     * @return mixed
     */
    public function following()
    {
        return $this->morphMany(Follows::class, 'user', 'followable_type')->where('deleted_at', null);
    }

    /**
     * @return bool
     */
    public function getIsFollowedAttribute()
    {
        $follow = $this->followers->filter(function ($follow) {
            return $follow->user_id = user()->id;
        })->first();

        return $follow ? : false;
    }

    /**
     * @return bool
     */
    public function getIsFollowingAttribute()
    {
        $follow = $this->following->filter(function ($follow) {
            return $follow->user_id = user()->id;
        })->first();

        return $follow ? : false;
    }
}
