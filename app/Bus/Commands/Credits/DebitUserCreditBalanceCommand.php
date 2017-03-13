<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Credits;

use Kabooodle\Models\Contracts\CreditTransactableInterface;
use Kabooodle\Models\User;

/**
 * Class DebitUserCreditBalanceCommand.
 */
final class DebitUserCreditBalanceCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var float
     */
    public $debitAmount;

    /**
     * @var string
     */
    public $type;

    /**
     * DebitUserCreditBalanceCommand constructor.
     *
     * @param User   $actor
     * @param float  $debitAmount
     * @param string $type
     */
    public function __construct(User $actor, $debitAmount, $type = CreditTransactableInterface::TYPE_DEBIT)
    {
        $this->actor = $actor;
        $this->debitAmount = $debitAmount;
        $this->type = $type;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @return float
     */
    public function getDebitAmount()
    {
        return $this->debitAmount;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
