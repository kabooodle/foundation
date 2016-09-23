<?php

namespace Kabooodle\Models\Traits;

use Kabooodle\Models\CreditTransactionsLog;
use Kabooodle\Models\Contracts\CreditTransactableInterface;

/**
 * Class CreditTransactableTrait
 * @package Kabooodle\Models\Traits
 */
trait CreditTransactableTrait
{
    public static function bootCreditTransactableTrait()
    {
        self::creating(function (CreditTransactableInterface $model) {
            // check again that the user has sufficient credits for this transaction.
            if (!$model->user->hasSufficientCredits($model->creditTransactionAmount())) {
                return false;
            }
        });

        self::saved(function (CreditTransactableInterface $model) {
            $transactionAmount = $model->creditTransactionAmount();
            $transaction = new CreditTransactionsLog;
            $transaction->user_id = $model->user_id;
            $transaction->transactable_type = get_class($model);
            $transaction->transactable_id = $model->id;
            $transaction->abs_amount = abs($transactionAmount);
            $transaction->type = $model->getTransactionType();
            $transaction->save();
        });
    }
}