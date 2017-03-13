<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * TODO: Identify a better name that represends the business logic better.
 * FIXME: Ok, decided a "store in a store" or a MALL doesn't make sense right now.
 *
 * @deprecated August 27, 2016, Jake Toolson
 *
 * Class FlashsaleSellerStore
 * @package Kabooodle\Models
 */
class FlashsaleSellerStore extends BaseEloquentModel
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'flashsale_seller_store';

    /**
     * @var array
     */
    protected $casts = [
        'flashsale_id' => 'int',
        'seller_id' => 'int',
        'description' => 'string',
        'policies' => 'string',
        'enabled' => 'boolean'
    ];

    /**
     * @var array
     */
    protected $attributes = [
        'flashsale_id' => 0,
        'seller_id' => 0,
        'description' => null,
        'policies' => null,
        'enabled' => false
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'policies',
        'enabled'
    ];

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (boolean) $this->enabled == 1;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|FlashSales
     */
    public function flashsale()
    {
        return $this->belongsTo(FlashSales::class, 'flashsale_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|User
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
