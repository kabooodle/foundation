<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use DB;
use Carbon\Carbon;
use JonnyPickett\EloquentSTI\SingleTableInheritance;
use Kabooodle\Bus\Events\Listables\ListableQuantityUpdatedEvent;
use Kabooodle\Models\Contracts\ListableInterface;
use Kabooodle\Models\Contracts\Viewable;
use Kabooodle\Models\Traits\ArchivableTrait;
use Kabooodle\Models\Traits\ListableTrait;
use Kabooodle\Models\Traits\ViewableTrait;
use Kabooodle\Presenters\PresentableTrait;
use Sofa\Revisionable\Revisionable;
use Kabooodle\Models\Traits\TaggableTrait;
use Kabooodle\Models\Traits\LikeableTrait;
use Kabooodle\Models\Traits\FollowableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Traits\CommentableTrait;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use AlgoliaSearch\Laravel\AlgoliaEloquentTrait;
use Sofa\Revisionable\Laravel\RevisionableTrait;
use Kabooodle\Models\Contracts\LikeableInterface;
use Kabooodle\Models\Contracts\Commentable;

/**
 * Class ListableInterface
 * @package Kabooodle\Models
 */
class Listable extends BaseEloquentModel implements Commentable, LikeableInterface, Revisionable, Viewable
{
    use ArchivableTrait,
        CommentableTrait,
        FollowableTrait,
        LikeableTrait,
        ObfuscatesIdTrait,
        PresentableTrait,
        RevisionableTrait,
        SingleTableInheritance,
        SoftDeletes,
        TaggableTrait,
        ListableTrait,
        ViewableTrait;

    const INVENTORY_TYPE_ID = 1;
    const INVENTORY_GROUPING_TYPE_ID = 2;
    const TYPES = [
        self::INVENTORY_TYPE_ID => Inventory::class,
        self::INVENTORY_GROUPING_TYPE_ID => InventoryGrouping::class,
    ];

    /**
     * @var array
     */
    protected $appends = [
        'cover_photo',
        'hash_id',
    ];

    /**
     * @var array
     */
    protected $with = [
        'files',
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
        'listable_type_id' => 1,
        'subclass_name' => Inventory::class,
        'inventory_type_id' => null,
        'inventory_type_styles_id' => null,
        'inventory_sizes_id' => null,
        'name' => '',
        'description' => '',
        'cover_photo_file_id' => null,
        'barcode' => null,
        'initial_qty' => null,
        'date_received' => '',
        'price_usd' => 0.0,
        'wholesale_price_usd' => 0.0,
        'locked' => true,
        'auto_add' => true,
        'max_quantity' => true,
    ];

    /**
     * @var array
     */
    protected $casts = [
        'uuid' => 'string',
        'user_id' => 'int',
        'listable_type_id' => 'int',
        'subclass_name' => 'string',
        'inventory_type_id' => 'int',
        'inventory_type_styles_id' => 'int',
        'inventory_sizes_id' => 'int',
        'name' => 'string',
        'description' => 'string',
        'barcode' => 'string',
        'date_received' => 'date',
        'price_usd' => 'double',
        'wholesale_price_usd' => 'double',
        'locked' => 'boolean',
        'auto_add' => 'boolean',
        'max_quantity' => 'boolean',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'uuid',
        'listable_type_id',
        'subclass_name',
        'inventory_type_id',
        'inventory_type_styles_id',
        'inventory_sizes_id',
        'price_usd',
        'wholesale_price_usd',
        'cover_photo_file_id',
        'name',
        'slug',
        'description',
        'barcode',
        'initial_qty',
        'date_received',
        'tags',
        'locked',
        'auto_add',
        'max_quantity',
    ];

    /**
     * @var string
     */
    protected $table = 'listables';

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

            if(! $model->slug || $model->slug !== $model->getUUID()) {
                $model->slug = $model->getUUID();
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
     * @return string
     */
    public function getName() : string
    {
        return $this->attributes['name'];
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
        return $this->claims()->whereNull('accepted')->whereNull('accepted_on')->whereNull('rejected_on');
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
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllImages()
    {
        return $this->images()->get();
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
            ->join('inventory', 'listing_items.listable_id', '=', 'listables.id')
            ->where('watchables.watchable_type', ListingItems::class)
            ->where('watchables.deleted_at', null)
            ->select('users.*')
            ->get();

        return collect($watches);
    }

    /**
     * @return string
     */
    public function getNameAttribute() : string
    {
        return $this->getName();
    }

    /**
     * @return int
     */
    public function getAvailableQuantityAttribute()
    {
        return $this->getAvailableQuantity();
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
