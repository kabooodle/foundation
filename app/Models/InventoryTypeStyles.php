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
 * Class InventoryTypeStyles
 * @package Kabooodle\Models
 */
class InventoryTypeStyles extends BaseEloquentModel implements Revisionable
{
    use RevisionableTrait, SoftDeletes;

    /**
     * @var array
     */
    protected $with = [
        'sizes'
    ];

    /**
     * @var string
     */
    protected $table = 'inventory_type_styles';

    /**
     * @var array
     */
    protected $attributes = [
        'inventory_type_id' => 0,
        'name' => '',
        'slug' => '',
        'sort_order' => 0,
//        'wholesale_price_usd' => 0.0,
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'inventory_type_id',
        'name',
        'slug',
        'sort_order',
        'wholesale_price_usd',
        'suggested_price_usd',
        'wholesale_price_usd_less_5_percent'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->inventoryType();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventoryType()
    {
        return $this->belongsTo(InventoryType::class, 'inventory_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sizes()
    {
        return $this->belongsToMany(InventorySizes::class, 'inventory_styles_sizes', 'inventory_type_style_id', 'inventory_size_id')->orderBy('sort_order');
    }
}
