<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Credits;

use Kabooodle\Models\User;
use Kabooodle\Models\CreditTransactionsLog;
use Kabooodle\Bus\Events\Credits\UserCreditsDebitFailed;
use Kabooodle\Bus\Events\Credits\UserCreditsDebitedEvent;
use Kabooodle\Models\Contracts\CreditTransactableInterface;
use Kabooodle\Bus\Commands\Credits\LogUserCreditTransactionCommand;
use Kabooodle\Foundation\Exceptions\Credits\InsufficientBalanceException;

/**
 * Class LogUserCreditTransactionCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Credits
 */
class LogUserCreditTransactionCommandHandler
{
    /**
     * @param LogUserCreditTransactionCommand $command
     *
     * @return CreditTransactionsLog
     * @throws InsufficientBalanceException
     */
    public function handle(LogUserCreditTransactionCommand $command)
    {
        $actor = $command->getActor();
        $transactable = $command->getTransactable();
        $toBeDebited = $transactable->creditTransactionAmount();

        $balance = $actor->getAvailableBalance();

        if (!$this->hasSufficientBalance($balance, $toBeDebited)) {
            event(new UserCreditsDebitFailed($actor, $transactable));
            throw new InsufficientBalanceException('Insufficient Balance: ' . $balance);
        }

        $transaction = $this->storeCreditTransaction($actor, $transactable);

        event(new UserCreditsDebitedEvent($actor, $transaction));

        return $transaction;
    }

    /**
     * @param User                        $actor
     * @param CreditTransactableInterface $transactable
     *
     * @return CreditTransactionsLog
     */
    public function storeCreditTransaction(User $actor, CreditTransactableInterface $transactable)
    {
        $transaction = new CreditTransactionsLog;
        $transaction->user_id = $actor->id;
        $transaction->transactable_id = $transactable->id;
        $transaction->transactable_type = $transactable;
        $transaction->abs_amount = $transactable->creditTransactionAmount();
        $transaction->type = CreditTransactionsLog::TYPE_DEBIT;
        $transaction->save();

        return $transaction;
    }

    /**
     * TODO: Handle strings that are passed as debits.
     *
     * @param int $balance
     * @param int $toBeDebited
     *
     * @return bool
     */
    public function hasSufficientBalance($balance, $toBeDebited)
    {
        $toBeDebited = abs(intval($toBeDebited));

        return (($balance) - ($toBeDebited)) > 0;
    }
}
