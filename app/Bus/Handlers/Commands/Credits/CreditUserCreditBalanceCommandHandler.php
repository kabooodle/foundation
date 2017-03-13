<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Handlers\Commands\Credits;

use Kabooodle\Bus\Commands\Credits\CreditUserCreditBalanceCommand;
use Kabooodle\Models\Contracts\CreditTransactableInterface;
use Kabooodle\Models\CreditBalance;

/**
 * Class CreditUserCreditBalanceCommandHandler
 * @package Kabooodle\Bus\Handlers\Commands\Credits
 */
class CreditUserCreditBalanceCommandHandler
{
    /**
     * @param CreditUserCreditBalanceCommand $command
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
   public function handle(CreditUserCreditBalanceCommand $command)
   {
       $actor = $command->getActor();
       $type = $command->getType();

       if ($type === CreditTransactableInterface::TYPE_DEBIT) {
           $creditAmount = - $command->getCreditAmount();
       } else {
           $creditAmount = $command->getCreditAmount();
       }

       $creditBalance = CreditBalance::updateOrCreate([
           'user_id' => $actor->id
       ], [
           'last_transaction_amount_of' => $creditAmount
       ]);

       return $creditBalance;
   }
}
