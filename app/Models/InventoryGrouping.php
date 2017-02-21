<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use DB;
use Carbon\Carbon;
use Kabooodle\Bus\Events\Listables\ListableQuantityUpdatedEvent;
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
class InventoryGrouping extends BaseEloquentModel implements Commentable, LikeableInterface, Revisionable, Listable, Viewable
{
    use CommentableTrait,
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
        'obfuscate_id',
        'name_with_variant',
        'name_uuid',
        'available_quantity',
        'cover_photo',
        'wholesale_price_usd',
    ];

    /**
     * @var array
     */
    protected $with = [
//        'tagged',
//        'flashsales',
//        'claims', // <- deathtrap of recursion
        'files',
//        'comments',
//        'sales'
    ];

    /**
     * @const string
     */
    const LISTING_ITEM_CLASS = ListingItemGrouping::class;

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
        'cover_photo_file_id' => null,
        'date_received' => '',
        'price_usd' => 0.0,
        'auto_add' => true,
        'max_quantity' => true,
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
        'auto_add' => 'boolean',
        'max_quantity' => 'boolean',
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
        'cover_photo_file_id',
        'auto_add',
        'max_quantity',
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
        $rules = [
            'name' => 'required|unique:inventory_groupings,name,NULL,id,deleted_at,NULL,user_id,',
            'price_usd' => 'required|min:0|numeric',
            'initial_qty' => 'required|min:1|numeric',
            'inventory' => 'required|array',
            'image' => 'required|array',
        ];

        $rules['name'] .= user()->id;

        return $rules;
    }

    /**
     * @param int $groupingId
     *
     * @return array
     */
    public static function getUpdateRules(int $groupingId)
    {
        $rules = self::getRules();

        $rules['name'] = str_replace('NULL,id', $groupingId.',id', $rules['name']);

        return $rules;
    }

    /**
     * @return array
     */
    public static function getMessages()
    {
        return [
            'name.required' => 'Your outfit must have a name.',
            'name.unique' => 'Your outfits must all have a unique name. You already have an outfit by the same name.',
            'price_usd.required' => 'Your outfit must have a price.',
            'price_usd.min' => 'Your outfit price must be a positive number.',
            'price_usd.numeric' => 'Your outfit price must be a positive number.',
            'initial_qty.required' => 'Your outfit must have a quantity.',
            'initial_qty.min' => 'Your outfit quantity must be at least one.',
            'initial_qty.numeric' => 'Your outfit quantity must be a number.',
            'inventory.required' => 'Your outfit must have inventory attached.',
            'inventory.array' => 'Your outfit must have inventory attached.',
            'image.required' => 'Your outfit must have an image attached.',
            'image.array' => 'Your outfit must have an image attached.',
        ];
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
                event(new ListableQuantityUpdatedEvent($model));
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
     * @param $value
     * @return float
     */
    public function getWholesalePriceUsdAttribute($value): float
    {
        return $this->inventoryItems()->sum('wholesale_price_usd');
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->attributes['name'];
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
        return $this->morphMany(Claims::class, 'listable');
    }

    /**
     * @return mixed
     */
    public function pendingClaims()
    {
        return $this->hasMany(Claims::class, 'listable_id')->whereNull('accepted')->whereNull('accepted_on')->whereNull('rejected_on');
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
        return $this->coverimage;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coverimage()
    {
        return $this->belongsTo(Files::class, 'cover_photo_file_id');
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
            ->join('inventory_groupings', 'listing_items.listable_id', '=', 'inventory_groupings.id')
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
        return $this->getName(). ' - '.$this->description;
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
        return $this->claims()->onHold()->count();
    }

    /**
     * @param int $amount
     * @return mixed
     */
    public function decrementInitialQty(int $amount = 1)
    {
        foreach ($this->inventoryItems as $item) {
            $item->decrementInitialQty($amount);
        }
        $this->initial_qty -= $amount;
        return $this->save();
    }

    /**
     * @param int $amount
     * @return mixed
     */
    public function incrementInitialQty(int $amount = 1)
    {
        foreach ($this->inventoryItems as $item) {
            $item->incrementInitialQty($amount);
        }
        $this->initial_qty += $amount;
        return $this->save();
    }

    /**
     * @param array $filters
     */
    public static function filter(array $filters)
    {
        $base = [
//            'style_id'      => [],
//            'size_id'       => [],
//            'has_claims'    => false,
//            'has_sales'     => false
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
