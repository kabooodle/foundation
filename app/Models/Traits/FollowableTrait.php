<?php

namespace Kabooodle\Models\Traits;

use Auth;
use Kabooodle\Models\User;

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
        return $this->morphToMany(User::class, 'followable')->whereDeletedAt(null);
    }

    /**
     * @return mixed
     */
    public function following()
    {
        return $this->morphedByMany(User::class, 'followable')->whereDeletedAt(null);
    }

    /**
     * @return bool
     */
    public function getIsFollowedAttribute()
    {
        $follow = $this->followers->filter(function($follow){
            return $follow->user_id = Auth::id();
        })->first();

        return $follow ? : false;
    }
}