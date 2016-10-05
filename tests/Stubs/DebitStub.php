<?php

namespace Kabooodle\Tests\Stubs;

use Kabooodle\Models\User;
use Kabooodle\Models\Contracts\CreditTransactableInterface;

/**
 * Class DebitStub
 * @package Kabooodle\Tests\Stubs
 */
class DebitStub implements CreditTransactableInterface
{
    public function creditTransactionAmount()
    {
        return -10;
    }

    public function getTransactionType()
    {
        return CreditTransactableInterface::TYPE_DEBIT;
    }

    public function user()
    {
        return factory(User::class)->make();
    }
}