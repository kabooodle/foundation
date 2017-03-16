<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

/**
 * Class Files
 * @package Kabooodle\Models
 */
class Files extends BaseEloquentModel
{
    /**
     * return array
     */
    const IMAGE_SIZES = [
        96,
        900
    ];

    /**
     * @var array
     */
    protected $appends = [
        'json',
        'original_location',
        'thumb',
    ];

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
        'bucket_name' => 'string',
    ];

    /**
     * @var array
     */
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
    ];

    /**
     * @return string
     */
    public function getJsonAttribute()
    {
        return json_encode([
            'id' => $this->id,
            'key' => $this->key,
            'bucket' => $this->bucket_name,
            'location' => $this->location,
            'fileable_id' => $this->fileable_id
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function fileable()
    {
        return $this->morphTo();
    }

    /**
     * @return mixed
     */
    public function getURL()
    {
        return $this->location;
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getLocationAttribute($value)
    {
        return useCDN() ? staticAsset($this->key, false) : $value;
    }

    /**
     * @return mixed
     */
    public function getOriginalLocationAttribute()
    {
        return $this->getOriginal('location');
    }

    /**
     * @return mixed|string
     */
    public function getThumbAttribute()
    {
        $key = '96x_'.$this->key;

        return useCDN() ? staticAsset($key, false) : $this->location;
    }
}
