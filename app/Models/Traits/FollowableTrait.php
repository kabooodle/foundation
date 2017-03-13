<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Auth;
use Kabooodle\Models\Follows;
use Kabooodle\Models\User;

/**
 * Class FollowableTrait
 * @package Kabooodle\Models\Traits
 */
trait FollowableTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->belongsToMany(User::class, 'followables', 'followable_id', 'user_id')
            ->whereFollowableType(User::class)
            ->where('followables.deleted_at', null)
            ->orderBy('username');
    }

    /**
     * @return bool
     */
    public function getIsFollowedAttribute()
    {
        if (user()) {
            return $this->followers->find(user()->id);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function getIsFollowingAttribute()
    {
        if (user()) {
            return $this->hasMany(Follows::class, 'followable_id')
                ->whereFollowableType(static::class)
                ->whereUserId(user()->id)
                ->whereDeletedAt(NULL)
                ->first();
        }
        return false;
    }
}
