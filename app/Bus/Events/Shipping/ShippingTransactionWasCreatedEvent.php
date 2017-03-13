<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2017. Kabooodle,LLC <help@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Shipping;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Kabooodle\Models\ShippingTransactions;

/**
 * Class ShippingTransactionWasCreatedEvent
 */
final class ShippingTransactionWasCreatedEvent
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var ShippingTransactions
     */
    protected $shippingTransaction;

    /**
     * ShippingTransactionWasCreatedEvent constructor.
     *
     * @param ShippingTransactions $transaction
     */
    public function __construct(ShippingTransactions $transaction)
    {
        $this->shippingTransaction = $transaction;
    }

    /**
     * @return ShippingTransactions
     */
    public function getShippingTransaction()
    {
        return $this->shippingTransaction;
    }
}
