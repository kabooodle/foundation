<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Sofa\Revisionable\Revisionable;
use Kabooodle\Models\Traits\UuidableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use AlgoliaSearch\Laravel\AlgoliaEloquentTrait;
use Sofa\Revisionable\Laravel\RevisionableTrait;
use Kabooodle\Models\Contracts\NotificationableInterface;

/**
 * Class Claims
 * @package Kabooodle\Models
 */
class Claims extends BaseEloquentModel implements NotificationableInterface, Revisionable
{
    use AlgoliaEloquentTrait, ObfuscatesIdTrait, RevisionableTrait, SoftDeletes, UuidableTrait;

    /**
     * @var array
     */
    protected $appends = [
        'claimer_item_date_price'
    ];

    /**
     * @var array
     */
    protected $with = [
//        'shoppable',
//        'claimer'
    ];

    /**
     * @var string
     */
    protected $table = 'claims';

    /**
     * @var array
     */
    protected $attributes = [
        'inventory_id' => 0,
        'claimed_by' => 0,
        'inventory_item_object_data' => '',
        'price' => 0,
        'shoppable_id' => 0,
        'shoppable_type' => null,
        'accepted' => null,
        'accepted_price' => null,
        'accepted_on' => null,
        'rejected_on' => null,
        'rejected_by' => null,
        'rejected_reason' => null
    ];

    /**
     * @var array
     */
    protected $casts = [
        'inventory_id' => 'int',
        'claimed_by' => 'int',
        'claim_accepted' => 'bool',
        'price' => 'float',
        'inventory_item_object_data' => 'object',
        'accepted' => 'bool',
        'accepted_price' => 'float',
        'accepted_on' => 'date',
        'rejected_on' => 'date',
        'rejected_by' => 'int',
        'rejected_reason' => 'string'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'inventory_id',
        'claimed_by',
        'inventory_id',
        'claimed_by',
        'inventory_item_object_data',
        'price',
        'accepted_price',
        'shoppable_id',
        'shoppable_type',
    ];

    public function setInventoryItemObjectDataAttribute($value)
    {
        $this->attributes['inventory_item_object_data'] = $value->toJson();
    }

    /**
     * @param $value
     *
     * @return array
     */
    public function getInventoryItemObjectDataAttribute($value)
    {
        return (array) json_decode($value, true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function shoppable()
    {
        return $this->morphTo('shoppable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventoryItem()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function claimer()
    {
        return $this->belongsTo(User::class, 'claimed_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function claimedBy()
    {
        return $this->claimer();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rejectedBy()
    {
        return $this->rejector();
    }

    /**
     * @return bool
     */
    public function wasRejected()
    {
        return (bool) ($this->accepted === false && $this->accepted_on == null && $this->rejected_by <> null);
    }

    /**
     * @return bool
     */
    public function isPending()
    {
        return (bool) ($this->accepted === null && $this->accepted_on === null && $this->rejected_by === null);
    }

    /**
     * @return bool
     */
    public function wasAccepted()
    {
        return (bool) ($this->accepted == 1 && $this->accepted_on <> null && $this->rejected_by === null);
    }

    /**
     * @return string
     */
    public function getClaimerItemDatePriceAttribute()
    {
        $claimer = $this->claimer;
        $item = $this->inventoryItem;
        $date = $this->updatedAtHuman();
        $price = $this->price;

        return $item->name.', '.$claimer->name.', $'.$price.', on: '.$date;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shipments()
    {
        return $this->hasMany(ShippingShipments::class, 'claim_id');
    }

    /**
     * @return mixed
     */
    public function shipmentTransaction()
    {
        $s = $this->shipments->load('transaction')->first();
        if ($s) {
            return $s->transaction;
        }

        return null;
    }
}
