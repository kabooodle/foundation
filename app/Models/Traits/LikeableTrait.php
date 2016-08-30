<?php

namespace Kabooodle\Models\Traits;

use Auth;
use Kabooodle\Models\User;

/**
 * Class LikeableTrait
 * @package Kabooodle\Models\Traits
 */
trait LikeableTrait
{
    /**
     * @return mixed
     */
    public function likes()
    {
        return $this->morphToMany(User::class, 'likeable')->whereDeletedAt(null);
    }

    /**
     * @return bool
     */
    public function getIsLikedAttribute()
    {
        $like = $this->likes->filter(function($like){
            return $like->user_id = Auth::id();
        })->first();

        return $like ? : false;
    }
}