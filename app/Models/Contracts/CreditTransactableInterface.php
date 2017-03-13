<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models\Contracts;

use Kabooodle\Models\User;

/**
 * Interface CreditTransactableInterface
 * @package Kabooodle\Models\Contracts
 */
interface CreditTransactableInterface
{
    const TYPE_DEBIT = 'debit';
    const TYPE_CREDIT = 'credit';
    const INCR_DEBIT = '-';
    const INCR_CREDIT = '+';

    /**
     * @return mixed
     */
    public function creditTransactionAmount();

    /**
     * @return mixed
     */
    public function getTransactionType();

    /**
     * @return User
     */
    public function user();
}
