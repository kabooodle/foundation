<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

/**
 * Class Files
 * @package Kabooodle\Models
 */
class Files extends BaseEloquentModel
{
    /**
     * @var string
     */
    protected $table = 'files';

    /**
     * @var array
     */
    protected $attributes = [
        'fileable_id' => 0,
        'fileable_type' => '',
        'location' => '',
        'key' => '',
        'bucket_name' => ''
    ];

    /**
     * @var array
     */
    protected $casts = [
        'fileable_id' => 'int',
        'fileable_type' => 'string',
        'location' => 'string',
        'key' => 'string',
        'bucket_name' => 'string'
    ];

    protected $fillable = [
        'fileable_id',
        'fileable_type',
        'location',
        'key',
        'bucket_name'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'fileable_type',
        'bucket_name',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function fileable()
    {
        return $this->morphTo();
    }
}