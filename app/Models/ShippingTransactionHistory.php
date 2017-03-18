<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

/**
 * Class ShippingTransactionHistory
 */
class ShippingTransactionHistory extends BaseEloquentModel
{
    const STATII = [
        'UNKNOWN',
        'FAILURE',
        'CREATED',
        'LABEL PRINTED',
        'PROCESSING',
        'IN TRANSIT',
        'DELIVERED',
        'RETURNED'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'status_date',
    ];

    /**
     * @var string
     */
    protected $table = 'shipping_transactions_history';

    /**
     * @var array
     */
    protected $attributes = [
        'shipping_transaction_id' => 0,
        'payload' => [],
        'status' => 'UNKNOWN',
        'status_details' => '',
        'status_date' =>'',
        'status_location' => [],
        'tracking_history' => []
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'shipping_transaction_id',
        'payload',
        'status',
        'status_details',
        'status_date',
        'status_location',
        'tracking_history'
    ];

    /**
     * @param $value
     */
    public function setStatusLocationAttribute($value)
    {
        $this->attributes['status_location'] = json_encode($value);
    }

    /**
     * @param $value
     */
    public function setTrackingHistoryAttribute($value)
    {
        $this->attributes['tracking_history'] = json_encode($value);
    }

    /**
     * @param $value
     */
    public function setPayloadAttribute($value)
    {
        $this->attributes['payload'] = json_encode($value);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function getStatusLocationAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function getTrackingHistoryAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function getPayloadAttribute($value)
    {
        return json_decode($value, true);
    }
}
