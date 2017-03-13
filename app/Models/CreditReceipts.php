<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Sofa\Revisionable\Revisionable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sofa\Revisionable\Laravel\RevisionableTrait;
use Kabooodle\Models\Traits\CreditTransactableTrait;
use Kabooodle\Models\Contracts\CreditTransactableInterface;

/**
 * Class CreditTransactions
 * @package Kabooodle\Models
 */
class CreditReceipts extends BaseEloquentModel implements CreditTransactableInterface, Revisionable
{
    use CreditTransactableTrait, RevisionableTrait, SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'credit_receipts';

    /**
     * @var array
     */
    protected $attributes = [
        'user_id' => 0,
        'credit_charge_type_id' => 0,
        'stripe_invoice_id' => '',
        'stripe_charge_id' => '',
        'transaction_items' => '',
        'transaction_amount_dollars' => 0,
        'transaction_amount_cents' => 0,
        'stripe_raw_response' => ''
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->transaction_amount_dollars = centsToDollars($model->transaction_amount_cents, false);
        });
    }

    /**
     * @param $value
     */
    public function setStripeRawResponseAttribute($value)
    {
        $this->attributes['stripe_raw_response'] =  json_encode($value);
    }

    /**
     * @param $value
     */
    public function setTransactionItemsAttribute($value)
    {
        $this->attributes['transaction_items'] =  json_encode($value);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function getStripeRawResponseAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * @param $value
     *
     * @return mixed
     */
    public function getTransactionItemsAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creditChargeType()
    {
        return $this->belongsTo(CreditChargeTypes::class, 'credit_charge_type_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return number
     */
    public function creditTransactionAmount()
    {
        $chargeType = $this->creditChargeType;

        return $chargeType->credits_equiv;
    }

    /**
     * @return string
     */
    public function getTransactionType()
    {
        return CreditTransactableInterface::TYPE_CREDIT;
    }
}
