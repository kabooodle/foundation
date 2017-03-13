<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Credits;

use Kabooodle\Models\User;
use Kabooodle\Models\Contracts\CreditTransactableInterface;

/**
 * Class CreditUserCreditBalanceCommand.
 */
final class CreditUserCreditBalanceCommand
{
    /**
     * @var User
     */
    public $actor;

    /**
     * @var float
     */
    public $creditAmount;

    /**
     * @var string
     */
    public $type;

    /**
     * DebitUserCreditBalanceCommand constructor.
     *
     * @param User   $actor
     * @param        $creditAmount
     * @param string $type
     */
    public function __construct(User $actor, $creditAmount, $type = CreditTransactableInterface::TYPE_CREDIT)
    {
        $this->actor = $actor;
        $this->creditAmount = $creditAmount;
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
    public function getCreditAmount()
    {
        return $this->creditAmount;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
