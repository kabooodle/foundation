<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use DB;
use Carbon\Carbon;
use Sofa\Revisionable\Revisionable;
use Kabooodle\Models\Traits\TaggableTrait;
use Kabooodle\Models\Traits\LikeableTrait;
use Kabooodle\Models\Traits\ClaimableTrait;
use Kabooodle\Models\Traits\FollowableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use AlgoliaSearch\Laravel\AlgoliaEloquentTrait;
use Sofa\Revisionable\Laravel\RevisionableTrait;
use Kabooodle\Models\Contracts\LikeableInterface;

/**
 * Class Inventory
 * @package Kabooodle\Models
 */
class Inventory extends BaseEloquentModel implements LikeableInterface, Revisionable
{
    use AlgoliaEloquentTrait, ClaimableTrait, FollowableTrait, LikeableTrait, ObfuscatesIdTrait, SoftDeletes, TaggableTrait, RevisionableTrait;

    /**
     * @var array
     */
    protected $with = [
//        'tagged',
//        'categories',
//        'flashsales',
//        'claims'
//        'files'
    ];

    /**
     * @return array
     */
    public function getAlgoliaRecord()
    {
        return array_merge($this->toArray(), [
            'oid' => $this->getUUID(),
            'route' => route('shop.inventory.show', [$this->user->username, $this->getUUID()])
        ]);
    }

    /**
     * @var array
     */
    protected $attributes = [
        'user_id' => 0,
        'name' => '',
        'description' => '',
        'size' => '',
        'barcode' => null,
        'initial_qty' => 0,
        'date_received' => '',
        'price_usd' => 0.0
    ];

    /**
     * @var array
     */
    protected $casts = [
        'user_id' => 'int',
        'name' => 'string',
        'description' => 'string',
        'barcode' => 'string',
        'date_received' => 'date',
        'initial_qty' => 'int',
        'price_usd' => 'double'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'price_usd',
        'name',
        'description',
        'size',
        'barcode',
        'initial_qty',
        'date_received',
        'tags'
    ];

    /**
     * @var string
     */
    protected $table = 'inventory';

    /**
     * @return array
     */
    public static function getRules()
    {
        return [
            'name' => 'required',
            'description' => 'required',
            'initial_qty' => 'required|int',
            'price_usd' => 'required|numeric|digits_between:0,100000000',
            'categories' => 'required|exists:categories,id',
        ];
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            $model->date_received = Carbon::now();
        });
    }

    /**
     * @param array $attributes
     *
     * @return static
     */
    public static function factory(array $attributes)
    {
        return self::create($attributes);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->owner();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function categories()
    {
        return $this->belongsToMany(Categories::class, 'inventory_categories', 'inventory_id', 'category_id')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function flashsales()
    {
        return $this->belongsToMany(FlashSales::class, 'flashsale_items', 'inventory_id', 'flashsale_id')->withTimestamps()->withPivot(['id as pivot_id']);
    }

    /**
     * TODO: Identify a better relationship for flash sales and facebook sales.
     *
     * @return array|static[]
     */
    public function facebooksales()
    {
        return $this->hasMany(FacebookItems::class, 'inventory_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function claims()
    {
        return $this->hasMany(Claims::class, 'inventory_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function files()
    {
        return $this->morphMany(Files::class, 'fileable');
    }

    /**
     * @return null
     */
    public function firstImage()
    {
        $images = $this->files;
        if ($images->count() > 0) {
            return $images->sortBy('order')->first();
        }

        return null;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return number_format($this->price_usd, 2);
    }

    /**
     * @param int $qty
     *
     * @return bool
     */
    public function canSatisfyRequestedQuantityOf($qty = 1)
    {
        return $this->getAvailableQuantity() >= $qty;
    }

    /**
     * @return int
     */
    public function getAvailableQuantity()
    {
        return $this->initial_qty;
    }
}
