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
class Follows extends BaseEloquentModel
{
    use ObfuscatesIdTrait, SoftDeletes;

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
     * @var array
     */
    protected $hidden = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function followable()
    {
        return $this->morphTo('followable');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
