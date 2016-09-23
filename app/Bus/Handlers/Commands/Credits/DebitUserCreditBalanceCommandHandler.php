<?php

namespace Kabooodle\Bus\Handlers\Commands\Credits;

use Kabooodle\Models\CreditBalance;
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
     * @return CreditBalance
     */
    public function handle(DebitUserCreditBalanceCommand $command)
    {
        $actor = $command->getActor();
        $debitAmount = $command->getDebitAmount();

        $creditBalance = new CreditBalance;
        $creditBalance->user_id = $actor->id;
        $creditBalance->last_transaction_amount_of = $debitAmount;
        $creditBalance->save();

        return $creditBalance;
    }
}