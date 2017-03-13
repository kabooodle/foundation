<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Sofa\Revisionable\Revisionable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sofa\Revisionable\Laravel\RevisionableTrait;

/**
 * Class InventoryType
 * @package Kabooodle\Models
 */
class InventoryType extends BaseEloquentModel implements Revisionable
{
    use RevisionableTrait, SoftDeletes;

    const TYPE_CUSTOM = 'custom';

    /**
     * @var string
     */
    protected $with = [
        'styles'
    ];

    /**
     * @var string
     */
    protected $table = 'inventory_type';

    /**
     * @var array
     */
    protected $attributes = [
        'name' => '',
        'slug' => '',
        'description' => '',
        'active' => 1,
        'sort_order'=> 0
    ];

    /**
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'slug' => 'string',
        'description' => 'string',
        'active' => 'boolean',
        'sort_oder' => 'int'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'active',
        'sort_order'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function styles()
    {
        return $this->hasMany(InventoryTypeStyles::class, 'inventory_type_id')->orderBy('sort_order');
    }

    /**
     * @param $scope
     *
     * @return mixed
     */
    public function scopeLuLaRoe($scope)
    {
        return $scope->where('slug', 'lularoe');
    }

    /**
     * @param $scope
     *
     * @return mixed
     */
    public function scopeWithStylesAndSizes($scope)
    {
        return $scope->with(['styles', 'styles.sizes']);
    }
}
