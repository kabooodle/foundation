<?php

namespace Kabooodle\Models\Contracts;

/**
 * Interface CreditTransactableInterface
 * @package Kabooodle\Models\Contracts
 */
interface CreditTransactableInterface
{
    /**
     * @return mixed
     */
    public function creditTransactionAmount();
}