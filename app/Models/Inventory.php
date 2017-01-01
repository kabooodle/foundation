<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use DB;
use Carbon\Carbon;
use Kabooodle\Bus\Events\Inventory\InventoryQuantityUpdatedEvent;
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
    protected $appends = [
        'name_with_variant',
        'name_uuid'
    ];

    /**
     * @var array
     */
    protected $with = [
        'style',
        'styleSize',
//        'tagged',
//        'flashsales',
//        'claims', // <- deathtrap of recursion
        'files',
//        'comments',
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
        'uuid' => '',
        'inventory_type_id' => 0,
        'inventory_type_styles_id' => 0,
        'inventory_sizes_id' => 0,
        'name' => '',
        'description' => '',
        'barcode' => null,
        'initial_qty' => null,
        'date_received' => '',
        'price_usd' => 0.0,
        'wholesale_price_usd' => 0.0,
    ];

    /**
     * @var array
     */
    protected $casts = [
        'uuid' => 'string',
        'user_id' => 'int',
        'inventory_type_id' => 'int',
        'inventory_type_styles_id' => 'int',
        'inventory_sizes_id' => 'int',
        'name' => 'string',
        'description' => 'string',
        'barcode' => 'string',
        'date_received' => 'date',
        'price_usd' => 'double',
        'wholesale_price_usd' => 'double'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'uuid',
        'inventory_type_id',
        'inventory_type_styles_id',
        'inventory_sizes_id',
        'price_usd',
        'wholesale_price_usd',
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
            if(! $model->wholesale_price_usd || is_null($model->wholesale_price_usd)) {
                $model->wholesale_price_usd = $this->style->wholesale_price_usd;
            }
            if(!$model->uuid) {
                $model->uuid = str_random(16);
            }

            if ($model->isDirty('initial_qty')) {
                event(new InventoryQuantityUpdatedEvent($model));
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
     * @return User
     */
    public function getOwner(): User
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
     * @return string
     */
    public function getNameAndSize(): string
    {
        return $this->getName(). ' '.$this->styleSize->name;
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function size()
    {
        return $this->styleSize();
    }

    /**
     * @return mixed
     */
    public function flashsales()
    {
        return $this->listings()->where('type', Listings::TYPE_FLASHSALE);
    }

    /**
     * @return array|static[]
     */
    public function facebooksales()
    {
        return $this->listings()->where('type', Listings::TYPE_FACEBOOK);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function listings()
    {
        return $this->hasMany(ListingItems::class, 'inventory_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function claims()
    {
        return $this->hasMany(Claims::class, 'inventory_id');
    }

    /**
     * @return mixed
     */
    public function pendingClaims()
    {
        return $this->hasMany(Claims::class, 'inventory_id')->whereNull('accepted')->whereNull('accepted_on')->whereNull('rejected_on');
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
     * @return \Illuminate\Support\Collection
     */
    public function getWatchers()
    {
        $watches = DB::table('users')
            ->join('watchables', 'watchables.user_id', '=', 'users.id')
            ->join('listing_items', 'listing_items.id', '=', 'watchables.watchable_id')
            ->join('inventory', 'listing_items.inventory_id', '=', 'inventory.id')
            ->where('watchables.watchable_type', ListingItems::class)
            ->where('watchables.deleted_at', null)
            ->select('users.*')
            ->get();

        return collect($watches);
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
        return $this->initial_qty - $this->getOnHoldQuantity();
    }

    /**
     * @return int
     */
    public function getOnHoldQuantity()
    {
        return $this->claims()->whereVerified(false)->where('created_at', '>=', Carbon::now()->sub(onHoldInterval()))->count();
    }

    /**
     * @return string
     */
    public function getNameAttribute() : string
    {
        return $this->getName();
    }

    /**
     * @return string
     */
    public function getNameWithVariantAttribute() : string
    {
        return $this->getName(). ' - '.$this->size->name;
    }

    /**
     * @return string
     */
    public function getNameUuidAttribute() : string
    {
        return $this->getUUID();
    }

    /**
     * @return mixed
     */
    public function getCategoriesAttribute()
    {
        return $this->tags;
    }

    /**
     * @param $value
     * @return float
     */
    public function getWholesalePriceUsdAttribute($value)
    {
        return $value ? $value : $this->style->wholesale_price_usd;
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
}
