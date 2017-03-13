<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Kabooodle\Models\Comments;

/**
 * Class CommentableTrait
 * @package Kabooodle\Models\Traits
 */
trait CommentableTrait
{
    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments()
    {
        return $this->morphMany(Comments::class, 'commentable');
    }
}
