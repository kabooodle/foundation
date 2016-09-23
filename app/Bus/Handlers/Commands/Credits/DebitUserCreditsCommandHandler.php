<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Credits;

use Kabooodle\Models\User;
use Kabooodle\Models\CreditTransactions;
use Kabooodle\Bus\Events\Credits\UserCreditsDebitFailed;
use Kabooodle\Bus\Events\Credits\UserCreditsDebitedEvent;
use Kabooodle\Bus\Commands\Credits\DebitUserCreditsCommand;
use Kabooodle\Models\Contracts\CreditTransactableInterface;
use Kabooodle\Foundation\Exceptions\Credits\InsufficientBalanceException;

/**
 * Class DebitUserCreditsCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Credits
 */
class DebitUserCreditsCommandHandler
{
    /**
     * @param DebitUserCreditsCommand $command
     *
     * @return CreditTransactions
     * @throws InsufficientBalanceException
     */
    public function handle(DebitUserCreditsCommand $command)
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
     * @return CreditTransactions
     */
    public function storeCreditTransaction(User $actor, CreditTransactableInterface $transactable)
    {
        $transaction = new CreditTransactions;
        $transaction->user_id = $actor->id;
        $transaction->transactable_id = $transactable->id;
        $transaction->transactable_type = $transactable;
        $transaction->amount = $transactable->creditTransactionAmount();
        $transaction->type = CreditTransactions::TYPE_DEBIT;
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