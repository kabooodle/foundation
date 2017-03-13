<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Kabooodle\Foundatino\Exceptions\StaleDataException;
use Kabooodle\Foundation\Exceptions\Credits\InsufficientBalanceException;

/**
 * Class CreditBalance
 * @package Kabooodle\Models
 */
class CreditBalance extends BaseEloquentModel
{
    const VERSION_INCR = 1;

    /**
     * @var string
     */
    protected $table = 'credit_balance';

    /**
     * @var array
     */
    protected $attributes = [
        'user_id' => 0,
        'balance' => 0,
        'previous_balance_of' => 0,
        'last_transaction_amount_of' => 0,
        'version' => 0
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'last_transaction_amount_of',
    ];

    public static function boot()
    {
        parent::boot();

        self::saving(function (self $model) {
            $original = $model->getOriginal();
            $model->version = $model->version + self::VERSION_INCR;

            if ($original['version'] <> ($model->version -  self::VERSION_INCR)) {
                throw new StaleDataException;
            }

            if (! self::hasSufficientBalance($model, $model->last_transaction_amount_of)) {
                throw new InsufficientBalanceException;
            }

            $model->balance = ($model->balance) + ($model->last_transaction_amount_of);
            $model->previous_balance_of = $original['balance'];
        });
    }

    /**
     * @param array $attributes
     * @param array $values
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public static function updateOrCreate(array $attributes, array $values = [])
    {
        $instance = self::firstOrNew($attributes);

        $instance->fill($values)->save();

        return $instance;
    }

    /**
     * @param CreditBalance $model
     * @param null          $amount
     *
     * @return bool
     */
    public static function hasSufficientBalance(CreditBalance $model, $amount = null)
    {
        $amount  = $amount ? : $model->last_transaction_amount_of;

        return ($model->balance) + ($amount) >= 0;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @param $qry
     * @param $userId
     *
     * @return mixed
     */
    public function scopeForUser($qry, $userId)
    {
        return $qry->where('user_id', $userId);
    }
}
