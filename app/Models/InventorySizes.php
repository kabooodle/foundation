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
 * Class InventorySizes
 * @package Kabooodle\Models
 */
class InventorySizes extends BaseEloquentModel implements Revisionable
{
    use RevisionableTrait, SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'inventory_sizes';

    /**
     * @var array
     */
    protected $attributes = [
        'name' => '',
        'slug' => '',
        'sort_order' => 0
    ];

    /**
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'slug' => 'string',
        'sort_order' => 'int'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'sort_order'
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
        'pivot'
    ];
}
