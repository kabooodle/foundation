<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Sofa\Revisionable\Revisionable;
use Kabooodle\Models\Traits\UuidableTrait;
use Sofa\Revisionable\Laravel\RevisionableTrait;
use Kabooodle\Models\Traits\CreditTransactableTrait;
use Kabooodle\Models\Contracts\CreditTransactableInterface;

/**
 * Class ShippingTransactions
 * @package Kabooodle\Models
 */
class ShippingTransactions extends BaseEloquentModel implements CreditTransactableInterface, Revisionable
{
    use CreditTransactableTrait, RevisionableTrait, UuidableTrait;

    const RATE_ADDON = 0.10;

    /**
     * @var string
     */
    protected $table = 'shipping_transactions';

    /**
     * @var array
     */
    protected $attributes = [
        'uuid' => null,
        'user_id' => 0,
        'shipping_shipments_id' => 0,
        'shipping_shipments_uuid' => '',
        'transaction_id' => 0,
        'tracking_number' => '',
        'tracking_status' => [],
        'rate_data' => [],
        'rate_id' => 0,
        'rate_amount' => 0,
        'rate_amount_addon' => self::RATE_ADDON,
        'rate_final_amount' => self::RATE_ADDON,
        'tracking_url_provider' => '',
        'tracking_history' => [],
        'label_url' => '',
        'label_file_embedded' => '',
        'status' => '',
        'messages' => [],
        'raw_response' => [],
    ];

    public static function boot()
    {
        parent::boot();

        self::saving(function($model){
            $model->rate_amount_addon = self::RATE_ADDON;
        });
    }

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
    public function shipment()
    {
        return $this->belongsTo(ShippingShipments::class, 'shipping_shipments_id');
    }

    /**
     * @param $value
     */
    public function setTrackingStatusAttribute($value)
    {
        $this->attributes['tracking_status'] = json_encode($value);
    }

    /**
     * @param $value
     */
    public function setRateDataAttribute($value)
    {
        $this->attributes['rate_data'] = json_encode($value);
    }

    /**
     * @param $value
     */
    public function setRawResponseAttribute($value)
    {
        $this->attributes['raw_response'] = json_encode($value);
    }

    /**
     * @param $value
     */
    public function setMessagesAttribute($value)
    {
        $this->attributes['messages'] = json_encode($value);
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
     *
     * @return mixed
     */
    public function getTrackingStatusAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * @return mixed
     */
    public function creditTransactionAmount()
    {
        return $this->rate_final_amount;
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return CreditTransactableInterface::TYPE_DEBIT;
    }
}