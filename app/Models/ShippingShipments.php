<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Carbon\Carbon;
use Sofa\Revisionable\Revisionable;
use Kabooodle\Models\Traits\UuidableTrait;
use Kabooodle\Services\Shippr\RatesObject;
use Sofa\Revisionable\Laravel\RevisionableTrait;

/**
 * Class ShippingShipments
 * @package Kabooodle\Models
 */
class ShippingShipments extends BaseEloquentModel implements Revisionable
{
    use RevisionableTrait, UuidableTrait;

    /**
     * @var array
     */
    protected $appends = [
        'rates_as_raw'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'expires_on'
    ];

    /**
     * @var string
     */
    protected $table = 'shipping_shipments';

    /**
     * @var array
     */
    protected $attributes = [
        'uuid' => null,
        'user_id' => 0,
        'shipment_id' => '',
        'recipient_id' => '',
        'recipient_data' => [],
        'sender_id' => '',
        'sender_data' => [],
        'parcel_id' => '',
        'parcel_data' => [],
        'status' => 'WAITING',
        'shipment_state' => 'VALID',
        'rates_url' => '',
        'rates_list' => [],
        'shipping_parcel_template_id' => '',
        'messages' => ''
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // According to SHIPPO, rates expire after 7 days.
           $model->expires_on = Carbon::now()->addDays(7);
        });
    }

    /**
     * @param $value
     */
    public function setParcelDataAttribute($value)
    {
        $this->attributes['parcel_data'] = json_encode($value);
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
    public function setRatesListAttribute($value)
    {
        $this->attributes['rates_list'] = json_encode($value);
    }

    /**
     * @param $value
     */
    public function setRecipientDataAttribute($value)
    {
        $this->attributes['recipient_data'] = json_encode($value);
    }

    /**
     * @param $value
     */
    public function setSenderDataAttribute($value)
    {
        $this->attributes['sender_data'] = json_encode($value);
    }

    /**
     * @param $value
     *
     * @return MailingAddress
     */
    public function getSenderDataAttribute($value)
    {
        return MailingAddress::fromArray(json_decode($value, true));
    }

    /**
     * @param $value
     *
     * @return MailingAddress
     */
    public function getRecipientDataAttribute($value)
    {
        return MailingAddress::fromArray(json_decode($value, true));
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function getParcelDataAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parcelTemplate()
    {
        return $this->belongsTo(ShippingParcelTemplates::class, 'shipping_parcel_template_id');
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function getRatesListAttribute($value)
    {
        $_shipments = json_decode($value, true);
        $_shipments = collect($_shipments)->filter(function ($item) {
            return $item['object_state'] === 'VALID' && $item['available_shippo'] === true;
        })->sortBy(function ($item) {
            return $item['amount'];
        });
        $shipments = null;
        foreach ($_shipments as $shipment) {
            $shipments[] = new RatesObject($shipment);
        }

        return $shipments;
    }

    /**
     * @return mixed
     */
    public function getRecipientData()
    {
        return $this->recipient_data;
    }

    /**
     * @return mixed
     */
    public function getSenderData()
    {
        return $this->sender_data;
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
    public function claims()
    {
        return $this->belongsToMany(Claims::class, 'shipping_shipments_claims', 'shipping_shipments_id', 'claim_id');
    }

    /**
     * @return mixed
     */
    public function claim()
    {
        return $this->claims()->first();
    }

    /**
     * @return mixed
     */
    public function claimer()
    {
        return $this->claims()->first()->claimer();
    }

    /**
     * @return array|mixed
     */
    public function getRatesAsRawAttribute()
    {
        return json_encode($this->rates_list, JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param $rateID
     *
     * @return bool|RatesObject
     */
    public function getRateId($rateID)
    {
        $rates = $this->rates_list;
        /** @var RatesObject $rate */
        foreach ($rates as $rate) {
            if ($rate->getRateId() == $rateID) {
                return $rate;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getMeasurements()
    {
        return "{$this->parcel_data['length']}L x {$this->parcel_data['width']}W x {$this->parcel_data['height']}H {$this->parcel_data['distance_unit']}";
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function transaction()
    {
        return $this->hasOne(ShippingTransactions::class, 'shipping_shipments_id');
    }

    /**
     * @return string
     */
    public function getSenderOrigin()
    {
        $senderData = $this->getSenderData();

        return $senderData->city.', '.$senderData->state;
    }
}
