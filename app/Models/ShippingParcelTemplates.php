<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ShippingParcelTemplates
 * @package Kabooodle\Models
 */
class ShippingParcelTemplates extends BaseEloquentModel
{
    use SoftDeletes;

    const DIMENSIONS_NUMBER_FORMAT = 2;

    /**
     * @var array
     */
    protected $appends = [
        'name_with_dimensions',
        'dimensions'
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $table = 'shipping_parcel_templates';

    /**
     * @param $value
     *
     * @return string
     */
    public function getLengthAttribute($value)
    {
        return number_format($value, self::DIMENSIONS_NUMBER_FORMAT);
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getWidthAttribute($value)
    {
        return number_format($value, self::DIMENSIONS_NUMBER_FORMAT);
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getHeightAttribute($value)
    {
        return number_format($value, self::DIMENSIONS_NUMBER_FORMAT);
    }

    /**
     * @return string
     */
    public function getNameWithDimensionsAttribute()
    {
        return $this->name." (".$this->dimensions.")";
    }

    /**
     * @return string
     */
    public function getDimensionsAttribute()
    {
        return "{$this->length}L x {$this->width}W x {$this->height}H {$this->distance_unit}";
    }
}
