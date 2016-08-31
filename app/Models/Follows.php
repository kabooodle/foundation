<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
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
class Follows extends BaseEloquentModel
{
    use FollowableTrait, ObfuscatesIdTrait, SoftDeletes;

    const FOLLOWABLE_COL = 'followable';

    /**
     * @var string
     */
    protected $table = 'followables';

    /**
     * @var array
     */
    protected $attributes = [
        'user_id' => 'int',
        'followable_id' => 'int',
        'followable_type' => 'string'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'followable_id',
        'followable_type'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function flashsales()
    {
        return $this->morphedByMany(FlashSales::class, self::FOLLOWABLE_COL);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function groups()
    {
        return $this->morphedByMany(Groups::class, self::FOLLOWABLE_COL);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function users()
    {
        return $this->morphedByMany(Groups::class, self::FOLLOWABLE_COL);
    }
}
