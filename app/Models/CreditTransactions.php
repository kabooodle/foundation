<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Models;

use Sofa\Revisionable\Revisionable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sofa\Revisionable\Laravel\RevisionableTrait;

/**
 * Class CreditTransactions
 * @package Kabooodle\Models
 */
class CreditTransactions extends BaseEloquentModel implements Revisionable
{
    use RevisionableTrait, SoftDeletes;

    const TYPE_DEBIT = 'debit';
    const TYPE_CREDIT = 'credit';
    const INCR_DEBIT = '-';
    const INCR_CREDIT = '+';

    /**
     * @var string
     */
    protected $table = 'credit_transactions';

    /**
     * @var array
     */
    protected $attributes = [
        'user_id' => 0,
        'transactable_id' => 0,
        'transactable_type' => '',
        'transaction_amount' => 0,
        'amount' => 0,
        'incr' => '-',
        'previous_balance_of' => '',
        'type' => self::TYPE_DEBIT,
    ];

    public static function boot()
    {
        parent::boot();

        self::saving(function($model){
            if ($model->type == self::TYPE_DEBIT) {
                $model->transaction_amount = '-'.$model->amount;
                $model->incr = self::INCR_DEBIT;
            } else {
                $model->transaction_amount = $model->amount;
                $model->incr = self::INCR_CREDIT;
            }
            $model->previous_balance_of = self::where('user_id', $model->user_id)->sum('amount');
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
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function transactable()
    {
        return $this->morphTo();
    }
}