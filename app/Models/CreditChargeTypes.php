<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Illuminate\Support\Str;
use Sofa\Revisionable\Revisionable;
use Sofa\Revisionable\Laravel\RevisionableTrait;

/**
 * Class CreditChargeTypes
 * @package Kabooodle\Models
 */
class CreditChargeTypes extends BaseEloquentModel implements Revisionable
{
    use RevisionableTrait;

    /**
     * @var string
     */
    protected $table = 'credit_charge_types';

    /**
     * @var array
     */
    protected $attributes = [
        'name' => '',
        'slug' => '',
        'description' => '',
        'amount' => 0,
        'credits_equiv' => 0,
        'per_credit' => 0,
        'active' => true
    ];

    public static function boot()
    {
        parent::boot();

        parent::creating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getAmountAttribute($value)
    {
        return number_format($value, 0);
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getCreditsEquivAttribute($value)
    {
        return number_format($value, 0);
    }
}
