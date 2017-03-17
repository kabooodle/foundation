<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Kabooodle\Models\Traits\ObfuscatesIdTrait;
use Kabooodle\Presenters\Models\Shipping\ShippingTransactionPresenter;
use Kabooodle\Presenters\PresentableTrait;
use Sofa\Revisionable\Revisionable;
use Kabooodle\Services\Shippr\RatesObject;
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
    use CreditTransactableTrait, ObfuscatesIdTrait, PresentableTrait, RevisionableTrait, UuidableTrait;

    const RATE_ADDON = 0.03;

    const SHIPPING_STATII = [
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
        'shipping_status_updated_on',
    ];

    /**
     * @var array
     */
    protected $with = [
        'shipment',
        'shippingHistory'
    ];

    /**
     * @var string
     */
    protected $presenter = ShippingTransactionPresenter::class;

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
        'recipient_id' => 0,
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
        'shipping_status' => 'CREATED',
        'shipping_status_updated_on' => null
    ];

    public static function boot()
    {
        parent::boot();

        self::saving(function ($model) {
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shippingHistory()
    {
        return $this->hasMany(ShippingTransactionHistory::class, 'shipping_transaction_id');
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
     *
     * @return RatesObject
     */
    public function getRateDataAttribute($value)
    {
        $value = json_decode($value, true);

        return $value;
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

    /**
     * @return bool|null
     */
    public function isLabelPrinted()
    {
        return $this->shipping_status == 'LABEL PRINTED';
    }

    /**
     * @return mixed
     */
    public function getShippingStatusHuman()
    {
        return $this->shipping_status_updated_on->format('m-d-Y h:ia');
    }

    /**
     * @return mixed
     */
    public function getLatestHistory()
    {
        return $this->shippingHistory->count() > 0 ? $this->shippingHistory->last()->status : $this->shipping_status;
    }

    /**
     * @return bool|ShippingTransactionHistory
     */
    public function isWithCarrierHistory()
    {
        if ($this->shippingHistory->count() > 0 ) {
            return $this->shippingHistory->filter(function(ShippingTransactionHistory $history){
                return $history->status === 'PROCESSING' || $history->status === 'UNKNOWN';
            })->first();
        }

        return false;
    }

    /**
     * @return bool|ShippingTransactionHistory
     */
    public function isInTransitHistory()
    {
        if ($this->shippingHistory->count() > 0 ) {
            return $this->shippingHistory->filter(function(ShippingTransactionHistory $history){
                return $history->status === 'IN TRANSIT';
            })->first();
        }

        return false;
    }

    /**
     * @return bool|ShippingTransactionHistory
     */
    public function isDeliveredHistory()
    {
        if ($this->shippingHistory->count() > 0 ) {
            return $this->shippingHistory->filter(function(ShippingTransactionHistory $history){
                return $history->status === 'DELIVERED';
            })->first();
        }

        return false;
    }

    /**
     * @param $status
     *
     * @return string
     */
    public static function mapShippoStatiiToLocalStatii(string $status)
    {
        $mapped = [
            'UNKNOWN' => 'PROCESSING',
            'DELIVERED'  => 'DELIVERED',
            'TRANSIT'  => 'IN TRANSIT',
            'FAILURE'  => 'FAILURE',
            'RETURNED'  => 'RETURNED',
        ];

        return $mapped[$status];
    }
}
