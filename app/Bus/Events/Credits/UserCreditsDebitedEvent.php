<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Credits;

use Kabooodle\Models\User;
use Kabooodle\Bus\Events\Event;
use Illuminate\Queue\InteractsWithQueue;
use Kabooodle\Models\CreditTransactions;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class UserCreditsDebitedEvent
 * @package Kabooodle\Bus\Events\Credits
 */
final class UserCreditsDebitedEvent extends Event implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * @var User
     */
    public $actor;

    /**
     * @var CreditTransactions
     */
    public $transaction;

    /**
     * UserCreditsDebitedEvent constructor.
     *
     * @param User               $actor
     * @param CreditTransactions $transaction
     */
    public function __construct(User $actor, CreditTransactions $transaction)
    {
        $this->actor = $actor;
        $this->transaction = $transaction;
    }

    /**
     * @return User
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * @return CreditTransactions
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}