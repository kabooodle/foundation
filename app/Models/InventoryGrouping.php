<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use DB;
use Carbon\Carbon;
use Kabooodle\Bus\Events\Listables\ListableQuantityUpdatedEvent;
use Kabooodle\Models\Contracts\Claimable;
use Kabooodle\Models\Contracts\ListableInterface;
use Kabooodle\Models\Contracts\Viewable;
use Kabooodle\Models\Traits\ListableTrait;
use Kabooodle\Models\Traits\ViewableTrait;
use Kabooodle\Presenters\Models\InventoryGrouping\InventoryGroupingPresenter;
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
class InventoryGrouping extends Listable implements ListableInterface
{
    /**
     * @var array
     */
    protected $appends = [
        'obfuscate_id',
        'name_uuid',
        'available_quantity',
        'cover_photo',
        'wholesale_price_usd',
        'hash_id',
        'title',
        'listable_type_friendly_name'
    ];

    /**
     * @var array
     */
    protected $with = [
        'files',
    ];

    /**
     * @var string
     */
    protected $presenter = InventoryGroupingPresenter::class;

    /**
     * @return array
     */
    public static function getRules()
    {
        $rules = [
            'name' => 'required|unique:listables,name,NULL,id,deleted_at,NULL,subclass_name,'.InventoryGrouping::class.',user_id,',
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
            'name.unique' => 'You already have an outfit by the same name.',
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

        self::saving(function(self $model) {
            $model->name_alt = 'Outfit - ' . $model->name;
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
        return $this->name_alt;
    }

    /**
     * @return string
     */
    public function getTitleAttribute(): string
    {
        return $this->getTitle();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllImages()
    {
        $ids = array_merge([$this->cover_photo_file_id],$this->inventoryItems->pluck('cover_photo_file_id')->all());
        return Files::leftJoin('listables', 'files.fileable_id', '=', 'listables.id')
            ->where(function ($q) use ($ids) {
                $q->whereIn('files.id', $ids);
                $q->whereIn('fileable_type', [InventoryGrouping::class, Inventory::class]);
            })
            ->orderBy('files.fileable_type', 'desc')
            ->orderBy('listables.name_alt', 'desc')
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function inventoryItems()
    {
        return $this->belongsToMany(Inventory::class, 'inventory_groupings_inventory');
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
     * @return string
     */
    public function getEditRoute(): string
    {
        return route('shop.outfits.edit', [$this->user->username, $this->getUUID()]);
    }

    /**
     * @return string
     */
    public function getListableTypeFriendlyNameAttribute(): string
    {
        return 'outfit';
    }
}
