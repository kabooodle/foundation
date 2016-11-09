<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

/**
 * Class ShippingWebhooks
 * @package Kabooodle\Models
 */
class ShippingWebhooks extends BaseEloquentModel
{
    /**
     * @var string
     */
    protected $table = 'shipping_webhooks';

    /**
     * @var array
     */
    protected $attributes = [
        'shipping_transaction_id' => 'integer',
        'data' => 'string',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shippingTransaction()
    {
        return $this->belongsTo(ShippingTransactions::class, 'shipping_transaction_id');
    }

    /**
     * @param $value
     */
    public function setDataAttribute($value)
    {
        $this->attributes['data'] = json_encode($value);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function getDataAttribute($value)
    {
        return json_decode($value, true);
    }
}