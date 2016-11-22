<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Sofa\Revisionable\Revisionable;
use Kabooodle\Models\Traits\ClaimableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Sofa\Revisionable\Laravel\RevisionableTrait;
use Kabooodle\Models\Contracts\ShoppableInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class FacebookItems
 * @package Kabooodle\Models
 */
class FacebookItems extends BaseEloquentModel implements Revisionable, ShoppableInterface
{
    use ClaimableTrait, ObfuscatesIdTrait, RevisionableTrait, SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'facebook_items';

    /**
     * @var array
     */
    protected $casts = [
        'facebook_node_id' => 'int',
        'seller_id' => 'int',
        'inventory_id' => 'int',
        'facebook_post_id' => 'int',
        'facebook_posted_at' => 'date'
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'facebook_node_id' => 0,
        'seller_id' => 0,
        'inventory_id' => 0,
        'facebook_post_id' => 0,
        'facebook_posted_at' => null
    ];

    /**
     * @param $value
     */
    public function setFacebookParametersAttribute($value)
    {
        $this->attributes['facebook_parameters'] = json_encode($value);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function getFacebookParametersAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
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
     * @return BelongsTo
     */
    public function inventoryItem(): BelongsTo
    {
        return $this->inventory();
    }

    /**
     * @return mixed
     */
    public function claims()
    {
        return $this->morphMany(Claims::class, 'shoppable');
    }

    /**
     * @return string
     */
    public function getNameOfResource(): string
    {
        return 'Facebook Album';
    }
}
