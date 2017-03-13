<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Models;

use Kabooodle\Models\Contracts\ReceiptInterface;

/**
 * Class Receipt
 * @package Kabooodle\Models\Plans
 */
class Receipt implements ReceiptInterface
{
    public function totalAmount()
    {
        return $this->transaction->transaction_amount;
    }

    public function getUserEmail()
    {
        return $this->user->email;
    }

    public function receiptDate()
    {
        return $this->transaction->created_at;
    }

    public function receiptNumber()
    {
        return $this->transaction->transactable_id;
    }

    public function purchaseDate()
    {
        return $this->transaction->created_at;
    }

    public function discount()
    {
        // TODO: Implement discount() method.
    }

    public function tax()
    {
        // TODO: Implement tax() method.
    }

    public function previousBalance()
    {
        return $this->transaction->previous_balance_of;
    }

    public function description()
    {
        return ucfirst(trans($this->transaction->type.'s'));
    }
}
