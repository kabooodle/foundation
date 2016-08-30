<?php

namespace Kabooodle\Models;

use AlgoliaSearch\Laravel\AlgoliaEloquentTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Traits\AuthorableTrait;
use Kabooodle\Models\Traits\FollowableTrait;
use Kabooodle\Models\Traits\LikeableTrait;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Sofa\Revisionable\Laravel\RevisionableTrait;

/**
 * Class FlashsaleItems
 * @package Kabooodle\Models
 */
class FlashsaleItems extends BaseEloquentModel
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
        'quantity' => 'int',
        'base_price' => 'float',
        'base_price_discount' => 'float',
        'enabled' => 'boolean',
        'enable_on' => 'datetime'
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'flashsale_id' => 0,
        'seller_id' => 0,
        'inventory_id' => 0,
        'quantity' => 0,
        'base_price' => 0,
        'base_price_discount' => 0,
        'description' => null,
        'policies' => null,
        'enabled' => false,
        'enable_on' => null,
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'policies',
        'enabled'
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
}