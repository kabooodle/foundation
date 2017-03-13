<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

/**
 * Class ShippingQueue
 * @package Kabooodle\Models
 */
class ShippingQueue extends BaseEloquentModel
{
    /**
     * @var string
     */
    protected $table = 'shipping_queue';

    /**
     * @var array
     */
    protected $attributes = [
        'claim_id' => 0,
        'user_id' => 0
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'claim_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function claim()
    {
        return $this->belongsTo(Claims::class, 'claim_id');
    }
}
