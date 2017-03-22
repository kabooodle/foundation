<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Traits;

use Kabooodle\Models\CreditTransactionsLog;
use Kabooodle\Models\Contracts\CreditTransactableInterface;
use Kabooodle\Foundation\Exceptions\Credits\InsufficientBalanceException;

/**
 * Class CreditTransactableTrait
 * @package Kabooodle\Models\Traits
 */
trait CreditTransactableTrait
{
    public static function bootCreditTransactableTrait()
    {
        // This creates a nesting bug that throws an exception
//        self::creating(function (CreditTransactableInterface $model) {
//            // check again that the user has sufficient credits for this transaction.
//            if ($model->getTransactionType() == CreditTransactableInterface::TYPE_DEBIT) {
//                if (!$model->user->hasSufficientCredits($model->creditTransactionAmount())) {
//                    throw new InsufficientBalanceException;
//                }
//            }
//        });

        self::saved(function (CreditTransactableInterface $model) {
            $transactionAmount = $model->creditTransactionAmount();
            $transaction = new CreditTransactionsLog;
            $transaction->user_id = $model->user_id;
            $transaction->transactable_type = get_class($model);
            $transaction->transactable_id = $model->id;
            $transaction->abs_amount = abs($transactionAmount);
            $transaction->transaction_amount = $transactionAmount;
            $transaction->type = $model->getTransactionType();
            $transaction->save();
        });
    }
}
