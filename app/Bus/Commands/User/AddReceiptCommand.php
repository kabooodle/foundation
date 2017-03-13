<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Commands\User;

use Kabooodle\Models\CreditTransactionsLog;
use Kabooodle\Models\User;

/**
 * Class AddReceiptCommand
 * @package Kabooodle\Bus\Commands\User
 */
final class AddReceiptCommand
{
    public $user;

    public $transaction;


    public function __construct(User $user, CreditTransactionsLog $transaction)
    {
        $this->user = $user;
        $this->transaction = $transaction;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
