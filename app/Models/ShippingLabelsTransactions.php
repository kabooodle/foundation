<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Sofa\Revisionable\Laravel\RevisionableTrait;

/**
 * Class ShippingLabels
 * @package Kabooodle\Models
 */
class ShippingLabelsTransactions extends BaseEloquentModel
{
    use RevisionableTrait, SoftDeletes;

    const TYPE_DEBIT = 'debit';
    const TYPE_CREDIT = 'credit';
    const INCR_DEBIT = '-';
    const INCR_CREDIT = '+';

    /**
     * @var string
     */
    protected $table = 'shipping_labels_transactions';

    /**
     * @var array
     */
    protected $attributes = [
        'user_id' => 0,
        'shipping_transaction_id' => 0,
        'quantity' => 0,
        'transaction_quantity' => 0,
        'source' => '',
        'incr' => '-',
        'type' => self::TYPE_DEBIT
    ];

    public static function boot()
    {
        parent::boot();

        self::saving(function($model){
            if ($model->type == self::TYPE_DEBIT) {
                $model->transaction_quantity = '-'.$model->quantity;
                $model->incr = self::INCR_DEBIT;
            } else {
                $model->transaction_quantity = $model->quantity;
                $model->incr = self::INCR_CREDIT;
            }
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
    public function shippingTransaction()
    {
        return $this->belongsTo(ShippingTransactions::class, 'shipping_transaction_id');
    }

    public function getAvailableLabels()
    {
//        SELECT personId,SUM(amount) as Total
//        FROM outputaddition
//        GROUP BY personID
    }
}