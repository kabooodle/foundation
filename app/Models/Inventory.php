<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Kabooodle\Models\Contracts\ListableInterface;

/**
 * Class Inventory
 * @package Kabooodle\Models
 */
class Inventory extends Listable implements ListableInterface
{
    /**
     * @var array
     */
    protected $appends = [
        'name_with_variant',
        'name_uuid',
        'available_quantity',
        'cover_photo',
        'hash_id',
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
//        'sales'
    ];

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

        self::saving(function(self $model) {
            $model->name_alt = $model->style->name . ' - ' . $model->size->name;
            if(! $model->wholesale_price_usd || is_null($model->wholesale_price_usd)) {
                $model->wholesale_price_usd = $model->style->wholesale_price_usd;
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
    public function getTitle() : string
    {
        return $this->getNameAndSize();
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->style->name;
    }

    /**
     * @return string
     */
    public function getNameAndSize(): string
    {
        return $this->style->name. ' '.$this->styleSize->name;
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groupings()
    {
        return $this->belongsToMany(InventoryGrouping::class, 'inventory_groupings_inventory');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function lockedGroupings()
    {
        return $this->groupings()->whereLocked(true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function unlockedGroupings()
    {
        return $this->groupings()->whereLocked(false);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groupingsClaims()
    {
        return $this->hasMany(Claims::class, 'listable_id')
            ->whereListableType(InventoryGrouping::class)
            ->whereIn('listable_id', $this->groupings()->lists('listables.id'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function lockedGroupingsClaims()
    {
        return $this->hasMany(Claims::class, 'listable_id')
            ->whereListableType(InventoryGrouping::class)
            ->whereIn('listable_id', $this->lockedGroupings()->lists('listables.id'));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function unlockedGroupingsClaims()
    {
        return $this->hasMany(Claims::class, 'listable_id')
            ->whereListableType(InventoryGrouping::class)
            ->whereIn('listable_id', $this->unlockedGroupings()->lists('listables.id'));
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
        return $this->initial_qty - ($this->lockedGroupings->sum('initial_qty') + $this->getOnHoldQuantity());
    }

    /**
     * @return int
     */
    public function getOnHoldQuantity(): int
    {
        return $this->claims()->onHold()->count() + $this->unlockedGroupingsClaims()->onHold()->count();
    }

    /**
     * @param int $amount
     * @return mixed
     */
    public function decrementInitialQty(int $amount = 1)
    {
        $this->initial_qty -= $amount;
        return $this->save();
    }

    /**
     * @param int $amount
     * @return mixed
     */
    public function incrementInitialQty(int $amount = 1)
    {
        $this->initial_qty += $amount;
        return $this->save();
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
