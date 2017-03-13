<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Kabooodle\Models\Traits\LikeableTrait;
use Kabooodle\Models\Traits\FollowableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;

/**
 * Class Groups
 * @package Kabooodle\Models
 */
class Likes extends BaseEloquentModel
{
    use LikeableTrait, FollowableTrait, ObfuscatesIdTrait, SoftDeletes;

    const LIKEABLE_COL = 'likable';

    /**
     * @var string
     */
    protected $table = 'likeables';

    /**
     * @var array
     */
    protected $attributes = [
        'user_id' => 'int',
        'likeable_id' => 'int',
        'likeable_type' => 'string'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'likeable_id',
        'likeable_type'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function flashsales()
    {
        return $this->morphedByMany(FlashSales::class, self::LIKEABLE_COL);
    }
}
