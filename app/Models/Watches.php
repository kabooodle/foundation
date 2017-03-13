<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;

/**
 * Class Groups
 * @package Kabooodle\Models
 */
class Watches extends BaseEloquentModel
{
    use ObfuscatesIdTrait, SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'watchables';

    /**
     * @var array
     */
    protected $attributes = [
        'user_id' => 'int',
        'watchable_id' => 'int',
        'watchable_type' => 'string'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'watchable_id',
        'watchable_type'
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
    public function watchable()
    {
        return $this->morphTo('watchable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function watcher()
    {
        return $this->user();
    }
}
