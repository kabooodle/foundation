<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use DB;
use Carbon\Carbon;
use Kabooodle\Bus\Events\Inventory\InventoryQuantityUpdatedEvent;
use Kabooodle\Models\Contracts\Claimable;
use Kabooodle\Models\Contracts\Listable;
use Kabooodle\Models\Contracts\Viewable;
use Kabooodle\Models\Traits\ListableTrait;
use Kabooodle\Models\Traits\ViewableTrait;
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
use Kabooodle\Models\Contracts\Commentable;

/**
 * Class InventoryGrouping
 * @package Kabooodle\Models
 */
class InventoryGrouping extends BaseEloquentModel implements Commentable, LikeableInterface, Revisionable, Listable, Claimable, Viewable
{
    use ClaimableTrait,
        CommentableTrait,
        FollowableTrait,
        LikeableTrait,
        ObfuscatesIdTrait,
        RevisionableTrait,
        SoftDeletes,
        TaggableTrait,
        ListableTrait,
        ViewableTrait;

    /**
     * @var array
     */
    protected $appends = [
        'name_with_variant',
        'name_uuid',
        'available_quantity',
        'cover_photo',
    ];

    /**
     * @var array
     */
    protected $with = [
//        'style',
//        'styleSize',
//        'tagged',
//        'flashsales',
//        'claims', // <- deathtrap of recursion
        'files',
//        'comments',
//        'sales'
    ];

    /**
     * @var string
     */
    protected $listingItemClass = ListingItemGrouping::class;

    /**
     * @return array
     */
    public function getAlgoliaRecord()
    {
        return array_merge($this->toArray(), [
            'oid' => $this->getUUID(),
            'route' => route('shop.inventory-groupings.show', [$this->user->username, $this->getUUID()])
        ]);
    }

    /**
     * @var array
     */
    protected $attributes = [
        'user_id' => 0,
        'uuid' => '',
        'name' => '',
        'description' => '',
        'locked' => true,
        'barcode' => null,
        'initial_qty' => null,
        'cover_photo_file_key' => null,
        'cover_photo_file_id' => null,
        'date_received' => '',
        'price_usd' => 0.0,
    ];

    /**
     * @var array
     */
    protected $casts = [
        'uuid' => 'string',
        'user_id' => 'int',
        'name' => 'string',
        'description' => 'string',
        'locked' => 'boolean',
        'barcode' => 'string',
        'initial_qty' => 'int',
        'date_received' => 'date',
        'price_usd' => 'double',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'uuid',
        'price_usd',
        'name',
        'description',
        'locked',
        'barcode',
        'initial_qty',
        'cover_photo_file_key',
        'cover_photo_file_id',
        'date_received',
        'tags',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * @var string
     */
    protected $table = 'inventory_groupings';

    /**
     * @return array
     */
    public static function getRules()
    {
        return [
            'type_id' => 'required|in:188432',
            'style_id' => 'required|exists:inventory_type_styles,id',
            'price_usd' => 'required|min:0|digits_between:0,100000000|numeric',
            'wholesale_price_usd' => 'min:0|digits_between:0,100000000|numeric',
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
            'images' => 'required|array',
            'uuid' => 'required'
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

        self::saving(function(self $model){
            if(!$model->uuid) {
                $model->uuid = str_random(16);
            }

            if ($model->isDirty('initial_qty')) {
                event(new InventoryGroupingQuantityUpdatedEvent($model));
                return true;
            }
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->getName();
    }

    /**
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function inventoryItems()
    {
        return $this->belongsToMany(Inventory::class, 'inventory_groupings_inventory');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function claims()
    {
        return $this->morphMany(Claims::class, 'claimable');
    }

    /**
     * @return mixed
     */
    public function pendingClaims()
    {
        return $this->hasMany(Claims::class, 'listed_id')->whereNull('accepted')->whereNull('accepted_on')->whereNull('rejected_on');
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
     * @return mixed
     */
    public function getCoverPhotoAttribute()
    {
//        return $this->files()->find($this->cover_photo_file_id)->location;
        return useCDN() ? staticAsset($this->cover_photo_file_key, false) : 'https://'.env('AWS_BUCKET').'.s3.amazonaws.com/'.$this->cover_photo_file_key;
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
     * @return \Illuminate\Support\Collection
     */
    public function getWatchers()
    {
        $watches = DB::table('users')
            ->join('watchables', 'watchables.user_id', '=', 'users.id')
            ->join('listing_items', 'listing_items.id', '=', 'watchables.watchable_id')
            ->join('inventory_groupings', 'listing_items.listed_id', '=', 'inventory_groupings.id')
            ->where('watchables.watchable_type', ListingItemGrouping::class)
            ->where('watchables.deleted_at', null)
            ->select('users.*')
            ->get();

        return collect($watches);
    }

    /**
     * @return string
     */
    public function getNameWithVariantAttribute() : string
    {
        return $this->getName(). ' - '.$this->size->name;
    }

    /**
     * @param $value
     * @return float
     */
    public function getWholesalePriceUsdAttribute($value): float
    {
        return $value ? $value : $this->style->wholesale_price_usd;
    }

    /**
     * @return int
     */
    public function getAvailableQuantity(): int
    {
        $selfAvailableQuantity = $this->initial_qty - $this->getOnHoldQuantity();
        if ($this->locked) {
            return $selfAvailableQuantity;
        } else {
            return min([$selfAvailableQuantity] + $this->getItemsAvailableQuantity());
        }
    }

    /**
     * @return int
     */
    public function getAvailableQuantityAttribute()
    {
        return $this->getAvailableQuantity();
    }

    /**
     * @return array
     */
    public function getItemsAvailableQuantity(): array
    {
        $availableValues = [];
        foreach ($this->inventoryItems as $item) {
            $availableValues[] = $item->getAvailableQuantity();
        }
        return $availableValues;
    }

    /**
     * @return int
     */
    public function getOnHoldQuantity(): int
    {
        return $this->claims()->whereVerified(false)->where('created_at', '>=', Carbon::now()->sub(onHoldInterval()))->count();
    }

    /**
     * @param array $filters
     */
    public static function filter(array $filters)
    {
        $base = [
            'style_id'      => [],
            'size_id'       => [],
            'has_claims'    => false,
            'has_sales'     => false
        ];

        $filters = $base + $filters;
    }

    /**
     * @return bool
     */
    public function hasViewableChild(): bool
    {
        return false;
    }

    /**
     * @return null
     */
    public function getViewableChild()
    {
        return null;
    }
}
