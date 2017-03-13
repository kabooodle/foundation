<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Credits;

use Kabooodle\Models\CreditBalance;
use Kabooodle\Models\Contracts\CreditTransactableInterface;
use Kabooodle\Bus\Commands\Credits\DebitUserCreditBalanceCommand;

/**
 * Class DebitUserCreditBalanceCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Credits
 */
class DebitUserCreditBalanceCommandHandler
{
    /**
     * @param DebitUserCreditBalanceCommand $command
     *
     * @return \Illuminate\Database\Eloquent\Model|CreditBalance
     */
    public function handle(DebitUserCreditBalanceCommand $command)
    {
        $actor = $command->getActor();
        $type = $command->getType();

        if ($type === CreditTransactableInterface::TYPE_DEBIT) {
            $debitAmount = - $command->getDebitAmount();
        } else {
            // throw exception
            $debitAmount = $command->getDebitAmount();
        }

        $creditBalance = CreditBalance::updateOrCreate([
           'user_id' => $actor->id
        ], [
            'last_transaction_amount_of' => $debitAmount
        ]);

        return $creditBalance;
    }
}
