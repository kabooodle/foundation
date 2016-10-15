<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Carbon\Carbon;
use Sofa\Revisionable\Revisionable;
use Kabooodle\Models\Traits\TaggableTrait;
use Kabooodle\Models\Traits\LikeableTrait;
use Kabooodle\Models\Traits\ClaimableTrait;
use Kabooodle\Models\Traits\FollowableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Traits\CommentableTrait;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use AlgoliaSearch\Laravel\AlgoliaEloquentTrait;
use Sofa\Revisionable\Laravel\RevisionableTrait;
use Kabooodle\Models\Contracts\LikeableInterface;
use Kabooodle\Models\Contracts\CommentableInterface;

/**
 * Class Inventory
 * @package Kabooodle\Models
 */
class Inventory extends BaseEloquentModel implements CommentableInterface, LikeableInterface, Revisionable
{
    use AlgoliaEloquentTrait,
        ClaimableTrait,
        CommentableTrait,
        FollowableTrait,
        LikeableTrait,
        ObfuscatesIdTrait,
        RevisionableTrait,
        SoftDeletes,
        TaggableTrait;

    /**
     * @var array
     */
    protected $with = [
        'style',
        'styleSize',
        'tagged',
        'flashsales',
//        'claims', // <- deathtrap of recursion
        'files',
        'comments',
        'sales'
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
        'inventory_type_id' => 0,
        'inventory_type_styles_id' => 0,
        'inventory_sizes_id' => 0,
        'name' => '',
        'description' => '',
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
        'inventory_type_id' => 'int',
        'inventory_type_styles_id' => 'int',
        'inventory_sizes_id' => 'int',
        'name' => 'string',
        'description' => 'string',
        'barcode' => 'string',
        'date_received' => 'date',
        'price_usd' => 'double'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'inventory_type_id',
        'inventory_type_styles_id',
        'inventory_sizes_id',
        'price_usd',
        'name',
        'description',
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
            'type_id' => 'required|in:188432',
            'style_id' => 'required|exists:inventory_type_styles,id',
            'price_usd' => 'required|min:0|digits_between:0,100000000|numeric',
            'sizings' => 'required|array',
            'sizings.*.size_id' => 'required|exists:inventory_sizes,id',
            'sizings.*.images' => 'required|array',
            'sizings.*.images.*.data' => 'required',
        ];
    }

    /**
     * @return array
     */
    public static function getUpdateRules()
    {
        $rules = self::getRules();
        $data = [
            'size_id' => 'required|exists:inventory_sizes,id',
            'images' => 'required|array'
        ];
        array_map(function($val, &$key) use (&$data) {
            if (in_array($key, ['style_id', 'price_usd'])) {
                $data[$key] = $val;
            }

            return $data;
        }, $rules, array_keys($rules));
        return $data;
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
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->style ? $this->style->name : $this->name;
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(InventoryType::class, 'inventory_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function style()
    {
        return $this->belongsTo(InventoryTypeStyles::class, 'inventory_type_styles_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function styleSize()
    {
        return $this->belongsTo(InventorySizes::class, 'inventory_sizes_id');
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales()
    {
        return $this->acceptedClaims();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function files()
    {
        return $this->morphMany(Files::class, 'fileable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function images()
    {
        return $this->files();
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
     * @return mixed
     */
    public function getRemainingImages()
    {
        $firstImage = $this->firstImage();
        return $this->images->filter(function($item) use ($firstImage){
            return $item->id <> $firstImage->id;
        });
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

    /**
     * @return string
     */
    public function getNameAttribute() : string
    {
        return $this->getName();
    }

    /**
     * @return mixed
     */
    public function getCategoriesAttribute()
    {
        return $this->tags;
    }
}
