<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\Credits;

use Kabooodle\Models\User;

/**
 * Class DebitUserCreditBalanceCommand
 * @package Kabooodle\Bus\Commands\Credits
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
     * DebitUserCreditBalanceCommand constructor.
     *
     * @param User $actor
     * @param float  $debitAmount
     */
    public function __construct(User $actor, $debitAmount)
    {
        $this->actor = $actor;
        $this->debitAmount = $debitAmount;
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
}