<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Sofa\Revisionable\Revisionable;
use Kabooodle\Models\Traits\LikeableTrait;
use Kabooodle\Models\Traits\AuthorableTrait;
use Kabooodle\Models\Traits\FollowableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use AlgoliaSearch\Laravel\AlgoliaEloquentTrait;
use Sofa\Revisionable\Laravel\RevisionableTrait;
use Kabooodle\Models\Contracts\ShoppableInterface;

/**
 * Class FlashsaleItems
 * @package Kabooodle\Models
 */
class FlashsaleItems extends BaseEloquentModel implements Revisionable, ShoppableInterface
{
    use AlgoliaEloquentTrait, AuthorableTrait, FollowableTrait, LikeableTrait,ObfuscatesIdTrait,  RevisionableTrait, SoftDeletes;

        /**
     * @var string
     */
    protected $table = 'flashsale_items';

    /**
     * @var array
     */
    protected $casts = [
        'flashsale_id' => 'int',
        'seller_id' => 'int',
        'inventory_id' => 'int',
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'flashsale_id' => 0,
        'seller_id' => 0,
        'inventory_id' => 0,
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
    ];
    /**
     * @var array
     */
    protected $hidden = [
        'created_by',
        'updated_by',
        'inventory_id',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|FlashSales
     */
    public function flashsale()
    {
        return $this->belongsTo(FlashSales::class, 'flashsale_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventory()
    {
        return $this->belongsto(Inventory::class, 'inventory_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|User
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (boolean) $this->enabled == 1;
    }

    /**
     * @return string
     */
    public function getNameOfResource()
    {
        return 'Flash Sale';
    }
}
