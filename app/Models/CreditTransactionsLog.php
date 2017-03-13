<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Ramsey\Uuid\Uuid;
use Sofa\Revisionable\Revisionable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Sofa\Revisionable\Laravel\RevisionableTrait;
use Kabooodle\Models\Contracts\CreditTransactableInterface;

/**
 * Class CreditTransactionsLog
 * @package Kabooodle\Models
 */
class CreditTransactionsLog extends BaseEloquentModel implements Revisionable
{
    use RevisionableTrait, SoftDeletes;

    const TYPE_DEBIT = CreditTransactableInterface::TYPE_DEBIT;
    const TYPE_CREDIT = CreditTransactableInterface::TYPE_CREDIT;
    const INCR_DEBIT = CreditTransactableInterface::INCR_DEBIT;
    const INCR_CREDIT = CreditTransactableInterface::INCR_CREDIT;

    /**
     * @var string
     */
    protected $table = 'credit_transactions_logs';

    /**
     * @var array
     */
    protected $attributes = [
        'user_id' => 0,
        'transactable_id' => 0,
        'transactable_type' => '',
        'transaction_amount' => 0,
        'abs_amount' => 0,
        'incr' => '-',
        'previous_balance_of' => '',
        'type' => self::TYPE_DEBIT,
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            $model->uuid = Uuid::uuid4();
        });

        self::saving(function ($model) {
            if ($model->type == self::TYPE_DEBIT) {
                $model->transaction_amount = '-'.$model->abs_amount;
                $model->incr = self::INCR_DEBIT;
            } else {
                $model->transaction_amount = $model->abs_amount;
                $model->incr = self::INCR_CREDIT;
            }
            $model->previous_balance_of = self::where('user_id', $model->user_id)->sum('transaction_amount');
        });
    }

    /**
     * @param $value
     */
    public function setTransactableTypeAttribute($value)
    {
        if ($value instanceof \Eloquent) {
            $value = get_class($value);
        }

        $this->attributes['transactable_type'] = $value;
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

    /**
     * @return mixed
     */
    public function date()
    {
        return $this->created_at;
    }

    /**
     * @return mixed
     */
    public function total()
    {
        return '$'.$this->abs_amount;
    }

    /**
     * @return bool
     */
    public function getClosedAttribute()
    {
        return true;
    }

    /**
     * @return string
     */
    public function description()
    {
        return 'Credits purchase for ';
    }
}
