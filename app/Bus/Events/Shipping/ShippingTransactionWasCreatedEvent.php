<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Shipping;

use Kabooodle\Bus\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Kabooodle\Models\ShippingTransactions;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class ShippingTransactionWasCreatedEvent
 * @package Kabooodle\Bus\Events\Shippings
 */
final class ShippingTransactionWasCreatedEvent extends Event implements ShouldQueue
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
        $this->Shippingtransaction = $transaction;
    }

    /**
     * @return ShippingTransactions
     */
    public function getShippingTransaction()
    {
        return $this->shippingTransaction;
    }
}