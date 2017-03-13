<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Traits\LikeableTrait;
use Kabooodle\Models\Traits\FollowableTrait;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;

/**
 * Class Images
 * @package Kabooodle\Models
 */
class Images extends BaseEloquentModel
{
    use LikeableTrait, FollowableTrait, SoftDeletes, ObfuscatesIdTrait;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function imageable()
    {
        return $this->morphTo();
    }
}
