<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Credits;

use Kabooodle\Models\User;
use Kabooodle\Bus\Events\Event;
use Illuminate\Queue\InteractsWithQueue;
use Kabooodle\Models\CreditTransactionsLog;
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
     * @var CreditTransactionsLog
     */
    public $transaction;

    /**
     * UserCreditsDebitedEvent constructor.
     *
     * @param User               $actor
     * @param CreditTransactionsLog $transaction
     */
    public function __construct(User $actor, CreditTransactionsLog $transaction)
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
     * @return CreditTransactionsLog
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
