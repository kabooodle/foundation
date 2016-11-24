<?php
/**
 * This file is part of Kabooodle.
 * Copyright (c) 2016. Jacob Toolson <jake@kabooodle.com>
 */

namespace Kabooodle\Bus\Events\Shipping;

use Kabooodle\Models\ShippingTransactions;
use Kabooodle\Models\ShippingTransactionHistory;

/**
 * Class ShippingTransactionStatusUpdatedEvent
 */
final class ShippingTransactionStatusUpdatedEvent
{
    /**
     * @var ShippingTransactions
     */
    public $shippingTransaction;

    /**
     * @var ShippingTransactionHistory
     */
    public $status;

    /**
     * @param ShippingTransactions $shippingTransaction
     */
    public function __construct(ShippingTransactions $shippingTransaction, ShippingTransactionHistory $status)
    {
        $this->shippingTransaction = $shippingTransaction;
        $this->status = $status;
    }

    /**
     * @return ShippingTransactions
     */
    public function getShippingTransaction(): ShippingTransactions
    {
        return $this->shippingTransaction;
    }

    /**
     * @return ShippingTransactionHistory
     */
    public function getStatus(): ShippingTransactionHistory
    {
        return $this->status;
    }
}